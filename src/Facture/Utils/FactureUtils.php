<?php


namespace AcMarche\Mercredi\Facture\Utils;


use AcMarche\Mercredi\Accueil\Repository\AccueilRepository;
use AcMarche\Mercredi\Entity\Accueil;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FactureAccueilRepository;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;

class FactureUtils
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var FacturePresenceRepository
     */
    private $facturePresenceRepository;
    /**
     * @var FactureAccueilRepository
     */
    private $factureAccueilRepository;
    /**
     * @var AccueilRepository
     */
    private $accueilRepository;

    public function __construct(
        PresenceRepository $presenceRepository,
        AccueilRepository $accueilRepository,
        FacturePresenceRepository $facturePresenceRepository,
        FactureAccueilRepository $factureAccueilRepository
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->factureAccueilRepository = $factureAccueilRepository;
        $this->accueilRepository = $accueilRepository;
    }

    /**
     * @param Tuteur $tuteur
     * @return Presence[]
     */
    public function getPresencesNonPayees(Tuteur $tuteur): array
    {
        $presencesAll = $this->presenceRepository->findPresencesByTuteur($tuteur);
        $presencesNonFacturees = [];
        foreach ($presencesAll as $presence) {
            if (!$this->facturePresenceRepository->findByPresence($presence)) {
                $presencesNonFacturees[] = $presence;
            }
        }

        return $presencesNonFacturees;
    }

    /**
     * @param Tuteur $tuteur
     * @return Accueil[]
     */
    public function getAccueilsNonPayes(Tuteur $tuteur): array
    {
        $all = $this->accueilRepository->findByTuteur($tuteur);
        $nonFacturees = [];
        foreach ($all as $accueil) {
            if (!$this->factureAccueilRepository->findByAccueil($accueil)) {
                $nonFacturees[] = $accueil;
            }
        }

        return $nonFacturees;
    }

    public function getPlainesNonPayes(Tuteur $tuteur)
    {
    }

}
