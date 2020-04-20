<?php

namespace AcMarche\Mercredi\Controller;

use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Form\TuteurType;
use AcMarche\Mercredi\Message\Tuteur\TuteurCreated;
use AcMarche\Mercredi\Message\Tuteur\TuteurDeleted;
use AcMarche\Mercredi\Message\Tuteur\TuteurUpdated;
use AcMarche\Mercredi\Repository\TuteurRepository;
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
     * @Route("/", name="mercredi_tuteur_index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render(
            '@AcMarcheMercredi/tuteur/index.html.twig',
            [
                'tuteurs' => $this->tuteurRepository->findAll(),
            ]
        );
    }

    /**
     * @Route("/new", name="mercredi_tuteur_new", methods={"GET","POST"})
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

            return $this->redirectToRoute('mercredi_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercredi/tuteur/new.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_tuteur_show", methods={"GET"})
     */
    public function show(Tuteur $tuteur): Response
    {
        return $this->render(
            '@AcMarcheMercredi/tuteur/show.html.twig',
            [
                'tuteur' => $tuteur,
            ]
        );
    }

    /**
     * @Route("/{id}/edit", name="mercredi_tuteur_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Tuteur $tuteur): Response
    {
        $form = $this->createForm(TuteurType::class, $tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurUpdated($tuteur->getId()));

            return $this->redirectToRoute('mercredi_tuteur_show', ['id' => $tuteur->getId()]);
        }

        return $this->render(
            '@AcMarcheMercredi/tuteur/edit.html.twig',
            [
                'tuteur' => $tuteur,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/{id}", name="mercredi_tuteur_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Tuteur $tuteur): Response
    {
        if ($this->isCsrfTokenValid('delete'.$tuteur->getId(), $request->request->get('_token'))) {
            $this->tuteurRepository->remove($tuteur);
            $this->tuteurRepository->flush();
            $this->dispatchMessage(new TuteurDeleted($tuteur->getId()));
        }

        return $this->redirectToRoute('mercredi_tuteur_index');
    }
}
