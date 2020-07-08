<?php

namespace AcMarche\Mercredi\Controller\Parent;

use AcMarche\Mercredi\Entity\Facture\Facture;
use AcMarche\Mercredi\Facture\Factory\FactureFactory;
use AcMarche\Mercredi\Facture\Factory\FacturePdfFactory;
use AcMarche\Mercredi\Facture\Repository\FactureRepository;
use AcMarche\Mercredi\Tuteur\Utils\TuteurUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class FactureController.
 *
 * @IsGranted("ROLE_MERCREDI_PARENT")
 * @Route("/facture")
 */
class FactureController extends AbstractController
{
    use GetTuteurTrait;

    /**
     * @var FactureRepository
     */
    private $factureRepository;

    /**
     * @var FactureFactory
     */
    private $factureFactory;
    /**
     * @var FacturePdfFactory
     */
    private $facturePdfFactory;
    /**
     * @var TuteurUtils
     */
    private $tuteurUtils;

    public function __construct(
        TuteurUtils $tuteurUtils,
        FactureRepository $factureRepository,
        FacturePdfFactory $facturePdfFactory
    ) {
        $this->factureRepository = $factureRepository;
        $this->facturePdfFactory = $facturePdfFactory;
        $this->tuteurUtils = $tuteurUtils;
    }

    /**
     * @Route("/", name="mercredi_parent_facture_index", methods={"GET","POST"})
     */
    public function index(): Response
    {
        $this->hasTuteur();
        $factures = $this->factureRepository->findFacturesByTuteur($this->tuteur);

        return $this->render(
            '@AcMarcheMercrediParent/facture/index.html.twig',
            [
                'factures' => $factures,
                'tuteur' => $this->tuteur,
            ]
        );
    }

    /**
     * @Route("/pdf/{uuid}", name="mercredi_parent_facture_pdf")
     */
    public function facture(Facture $facture): Response
    {
        $this->hasTuteur();
        $this->denyAccessUnlessGranted('tuteur_show', $this->tuteur);

        return $this->facturePdfFactory->generate($facture);
    }
}
