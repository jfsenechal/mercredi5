<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Repository\TuteurRepository;
use AcMarche\Mercredi\Tuteur\Message\TuteurCreated;
use AcMarche\Mercredi\Tuteur\Message\TuteurDeleted;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/tuteur")
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 */
class TuteurController extends AbstractController
{
    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(TuteurRepository $tuteurRepository)
    {
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * @Route("/", name="admin_mercredi_tuteur_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/index.html.twig',
            [
                'tuteurs' => $this->tuteurRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="admin_mercredi_tuteur_new", methods={"GET","POST"})
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

            return $this->redirectToRoute('admin_mercredi_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_tuteur_show", methods={"GET"})
     */
    public function show(Tuteur $tuteur): Response
    {
        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/show.html.twig',
            [
                'tuteur' => $tuteur,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="admin_mercredi_tuteur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tuteur $tuteur): Response
    {
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurUpdated($tuteur->getId()));

            return $this->redirectToRoute('admin_mercredi_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercrediAdmin/tuteur/edit.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="admin_mercredi_tuteur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tuteur $tuteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->request->get('_token'))) {
            $this->tuteurRepository->remove($tuteur);
            $this->tuteurRepository->flush();
            $this->dispatchMessage(new TuteurDeleted($tuteur->getId()));
        }

        return $this->redirectToRoute('admin_mercredi_tuteur_index');
    }
}