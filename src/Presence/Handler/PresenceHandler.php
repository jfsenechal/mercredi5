<?php

namespace AcMarche\Mercredi\Presence\Handler;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Facture\Repository\FacturePresenceRepository;
use AcMarche\Mercredi\Presence\Constraint\PresenceConstraints;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use AcMarche\Mercredi\Presence\Utils\PresenceUtils;
use Doctrine\ORM\NonUniqueResultException;

final class PresenceHandler
{
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var PresenceUtils
     */
    private $presenceUtils;
    /**
     * @var FacturePresenceRepository
     */
    private $facturePresenceRepository;
    /**
     * @var PresenceConstraints
     */
    private $presenceConstraints;

    public function __construct(
        PresenceRepository $presenceRepository,
        PresenceUtils $presenceUtils,
        FacturePresenceRepository $facturePresenceRepository,
        PresenceConstraints $presenceConstraints
    ) {
        $this->presenceRepository = $presenceRepository;
        $this->presenceUtils = $presenceUtils;
        $this->facturePresenceRepository = $facturePresenceRepository;
        $this->presenceConstraints = $presenceConstraints;
    }

    /**
     * @param Jour[] $days
     *
     * @throws NonUniqueResultException
     */
    public function handleNew(Tuteur $tuteur, Enfant $enfant, iterable $days): void
    {
        foreach ($days as $jour) {
            if (null !== $this->presenceRepository->isRegistered($enfant, $jour)) {
                continue;
            }

            if (! $this->checkConstraints($jour)) {
                continue;
            }

            $presence = new Presence($tuteur, $enfant, $jour);
            $this->presenceRepository->persist($presence);
        }
        $this->presenceRepository->flush();
    }

    public function handleForGrouping(Jour $jour, ?Ecole $ecole, bool $displayRemarque): array
    {
        $presences = $this->presenceRepository->findPresencesByJourAndEcole($jour, $ecole);

        $enfants = PresenceUtils::extractEnfants($presences, $displayRemarque);
        $this->presenceUtils->addTelephonesOnEnfants($enfants);

        return $this->presenceUtils->groupByGroupScolaire($enfants);
    }

    public function checkConstraints(Jour $jour): bool
    {
        $this->presenceConstraints->execute($jour);
        foreach ($this->presenceConstraints as $constraint) {
            if (! $constraint->check($jour)) {
                $constraint->addFlashError($jour);

                return false;
            }
        }

        return true;
    }

    public function isFactured(Presence $presence): bool
    {
        return (bool) $this->facturePresenceRepository->findByPresence($presence);
    }
}
