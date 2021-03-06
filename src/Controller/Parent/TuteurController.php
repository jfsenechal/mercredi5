<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Tuteur\Form\TuteurType;
use AcMarche\Mercredi\Tuteur\Message\TuteurUpdated;
use AcMarche\Mercredi\Tuteur\Repository\TuteurRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController.
 *
 * @Route("/tuteur")
 */
final class TuteurController extends AbstractController
{
    use GetTuteurTrait;

    /**
     * @var TuteurRepository
     */
    private $tuteurRepository;

    public function __construct(TuteurRepository $tuteurRepository)
    {
        $this->tuteurRepository = $tuteurRepository;
    }

    /**
     * @Route("/", name="mercredi_parent_tuteur_show", methods={"GET"})
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function show()
    {
        if ($t = $this->hasTuteur()) {
            return $t;
        }

        $tuteurIsComplete = TuteurUtils::coordonneesIsComplete($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/tuteur/show.html.twig',
            [
                'tuteurIsComplete' => $tuteurIsComplete,
                'tuteur' => $this->tuteur,
            ]
        );
    }

    /**
     * @Route("/edit", name="mercredi_parent_tuteur_edit", methods={"GET","POST"})
     * @IsGranted("ROLE_MERCREDI_PARENT")
     */
    public function edit(Request $request)
    {
        if ($t = $this->hasTuteur()) {
            return $t;
        }

        $form = $this->createForm(TuteurType::class, $this->tuteur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->tuteurRepository->flush();

            $this->dispatchMessage(new TuteurUpdated($this->tuteur->getId()));

            return $this->redirectToRoute('mercredi_parent_tuteur_show');
        }

        return $this->render(
            '@AcMarcheMercrediParent/tuteur/edit.html.twig',
            [
                'tuteur' => $this->tuteur,
                'form' => $form->createView(),
            ]
        );
    }
}
