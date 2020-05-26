<?php

namespace AcMarche\Mercredi\Admin\Controller;

use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Presence\Dto\ListingPresenceByMonth;
use AcMarche\Mercredi\Presence\Spreadsheet\SpreadsheetFactory;
use AcMarche\Mercredi\Search\SearchHelper;
use AcMarche\Mercredi\Utils\DateUtils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package AcMarche\Mercredi\Controller
 *
 * @IsGranted("ROLE_MERCREDI_ADMIN")
 * @Route("/export")
 */
class ExportController extends AbstractController
{
    /**
     * @var SpreadsheetFactory
     */
    private $spreadsheetFactory;
    /**
     * @var ListingPresenceByMonth
     */
    private $listingPresenceByMonth;
    /**
     * @var SearchHelper
     */
    private $searchHelper;

    public function __construct(
        SpreadsheetFactory $spreadsheetFactory,
        ListingPresenceByMonth $listingPresenceByMonth,
        SearchHelper $searchHelper
    ) {
        $this->spreadsheetFactory = $spreadsheetFactory;
        $this->listingPresenceByMonth = $listingPresenceByMonth;
        $this->searchHelper = $searchHelper;
    }

    /**
     * @Route("/presence/{id}", name="mercredi_admin_export_presence_xls")
     */
    public function default(Request $request, Jour $jour): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST);
        $date = $args['mois'];
        $listingPresences = $this->listingPresenceByMonth->create($date);
        $spreadsheet = $this->spreadsheetFactory->presenceXls($listingPresences);

        return $this->spreadsheetFactory->downloadXls($spreadsheet, 'presences.xls');
    }

    /**
     * @Route("/presence/mois/{one}", name="mercredi_admin_export_presence_mois_xls", requirements={"mois"=".+"}, methods={"GET"})
     * Requirement a cause du format "mois/annee"
     *
     * @param $mois
     * @param bool $one Office de l'enfance
     *
     */
    public function presenceByMonthXls(bool $one): Response
    {
        $args = $this->searchHelper->getArgs(SearchHelper::PRESENCE_LIST_BY_MONTH);
        $mois = $args['mois'];

        try {
            $date = DateUtils::createDateTimeFromDayMonth($mois);
        } catch (\Exception $e) {
            $this->addFlash('danger', $e->getMessage());

            return $this->redirectToRoute('mercredi_admin_presence_by_month');
        }

        $fileName = 'listing-'.$date->format('m-Y').'.xls';

        $listingPresences = $this->listingPresenceByMonth->create($date);

        if ($one) {
            $spreadsheet = $this->spreadsheetFactory->createXlsOne($date, $listingPresences);
        } else {
            $spreadsheet = $this->spreadsheetFactory->createXls($listingPresences);
        }

        return $this->spreadsheetFactory->downloadXls($spreadsheet, $fileName);
    }
}
