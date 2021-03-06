<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Note;
use AcMarche\Mercredi\Note\Form\NoteType;
use AcMarche\Mercredi\Note\Message\NoteCreated;
use AcMarche\Mercredi\Note\Message\NoteDeleted;
use AcMarche\Mercredi\Note\Message\NoteUpdated;
use AcMarche\Mercredi\Note\Repository\NoteRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/note")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class NoteController extends AbstractController
{
    /**
     * @var string
     */
    private const MERCREDI_ADMIN_NOTE_SHOW = 'mercredi_admin_note_show';
    /**
     * @var string
     */
    private const ID = 'id';
    /**
     * @var string
     */
    private const NOTE = 'note';
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var NoteRepository
     */
    private $noteRepository;

    public function __construct(NoteRepository $noteRepository)
    {
        $this->noteRepository = $noteRepository;
    }

    /**
     * @Route("/", name="mercredi_admin_note_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/note/index.html.twig',
            [
                'notes' => $this->noteRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new/", name="mercredi_admin_note_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->noteRepository->persist($note);
            $this->noteRepository->flush();

            $this->dispatchMessage(new NoteCreated($note->getId()));

            return $this->redirectToRoute(self::MERCREDI_ADMIN_NOTE_SHOW, [self::ID => $note->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/note/new.html.twig',
            [
                self::NOTE => $note,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/new/enfant/{id}", name="mercredi_admin_note_new_enfant", methods={"GET","POST"})
     */
    public function newForEnfant(Request $request, Enfant $enfant): Response
    {
        $note = new Note($enfant);
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->noteRepository->persist($note);
            $this->noteRepository->flush();

            $this->dispatchMessage(new NoteCreated($note->getId()));

            return $this->redirectToRoute(self::MERCREDI_ADMIN_NOTE_SHOW, [self::ID => $note->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/note/new.html.twig',
            [
                self::NOTE => $note,
                'enfant' => $enfant,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_note_show", methods={"GET"})
     */
    public function show(Note $note): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/note/show.html.twig',
            [
                self::NOTE => $note,
                'enfant' => $note->getEnfant(),
            ]
        );
    }

    /**
     * @Route("/enfant/{id}", name="mercredi_admin_note_enfant_show", methods={"GET"})
     */
    public function enfant(Enfant $enfant): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/note/index_by_enfant.html.twig',
            [
                'enfant' => $enfant,
                'notes' => $enfant->getNotes(),
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_note_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Note $note): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->noteRepository->flush();

            $this->dispatchMessage(new NoteUpdated($note->getId()));

            return $this->redirectToRoute(self::MERCREDI_ADMIN_NOTE_SHOW, [self::ID => $note->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/note/edit.html.twig',
            [
                self::NOTE => $note,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_note_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getId(), $request->request->get('_token'))) {
            $enfant = $note->getEnfant();
            $noteId = $note->getId();
            $this->noteRepository->remove($note);
            $this->noteRepository->flush();
            $this->dispatchMessage(new NoteDeleted($noteId));

            return $this->redirectToRoute('mercredi_admin_enfant_show', ['id' => $enfant->getId()]);
        }

        return $this->redirectToRoute('mercredi_admin_note_index');
    }
}
