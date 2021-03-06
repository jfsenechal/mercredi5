<?php

namespace AcMarche\Mercredi\Controller\Admin;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Tuteur\Form\SearchTuteurType;
use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Tuteur\Message\TuteurCreated;
use AcMarche\Mercredi\Tuteur\Message\TuteurDeleted;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tuteur")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
final class TuteurController extends AbstractController
{
    /**
     * @var string
     */
    private const FORM = 'form';
    /**
     * @var string
     */
    private const TUTEUR = 'tuteur';
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;
    /**
     * @var RelationRepository
     */
    private $relationRepository;
    /**
     * @var SearchHelper
     */
    private $searchHelper;

    public function __construct(
        TuteurRepository $tuteurRepository,
        RelationRepository $relationRepository,
        SearchHelper $searchHelper
    ) {
        $this->tuteurRepository = $tuteurRepository;
        $this->relationRepository = $relationRepository;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @Route("/", name="mercredi_admin_tuteur_index", methods={"GET","POST"})
     */
    public function index(Request $request): Response
    {
        $form = $this->createForm(SearchTuteurType::class);
        $form->handleRequest($request);
        $search = false;
        $tuteurs = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $this->searchHelper->saveSearch(SearchHelper::TUTEUR_LIST, $data);
            $search = true;
            $tuteurs = $this->tuteurRepository->search($data['nom']);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/index.html.twig',
            [
                'tuteurs' => $tuteurs,
                self::FORM => $form->createView(),
                'search' => $search,
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_admin_tuteur_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $tuteur = new Tuteur();
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->persist($tuteur);
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurCreated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/new.html.twig',
            [
                self::TUTEUR => $tuteur,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_admin_tuteur_show", methods={"GET"})
     */
    public function show(Tuteur $tuteur): Response
    {
        $relations = $this->relationRepository->findByTuteur($tuteur);

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/show.html.twig',
            [
                self::TUTEUR => $tuteur,
                'relations' => $relations,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_admin_tuteur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tuteur $tuteur): Response
    {
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurUpdated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_admin_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/edit.html.twig',
            [
                self::TUTEUR => $tuteur,
                self::FORM => $form->createView(),
            ]
        );
    }

    /**
     * //todo que faire si presence.
     *
     * @Route("/{id}", name="mercredi_admin_tuteur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tuteur $tuteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->request->get('_token'))) {
            $id = $tuteur->getId();
            $this->tuteurRepository->remove($tuteur);
            $this->tuteurRepository->flush();
            $this->dispatchMessage(new TuteurDeleted($id));
        }

        return $this->redirectToRoute('mercredi_admin_tuteur_index');
    }
}
