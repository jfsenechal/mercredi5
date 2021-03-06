<?php

namespace AcMarche\Mercredi\Presence\Dto;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Jour\Repository\JourRepository;
use AcMarche\Mercredi\Presence\Repository\PresenceRepository;
use DateTimeInterface;

final class ListingPresenceByMonth
{
    /**
     * @var Presence[]
     */
    private $presences;
    /**
     * @var Enfant[]
     */
    private $enfants;
    /**
     * @var JourListing[]
     */
    private $joursListing;
    /**
     * @var PresenceRepository
     */
    private $presenceRepository;
    /**
     * @var JourRepository
     */
    private $jourRepository;

    public function __construct(PresenceRepository $presenceRepository, JourRepository $jourRepository)
    {
        $this->presenceRepository = $presenceRepository;
        $this->jourRepository = $jourRepository;
    }

    public function create(DateTimeInterface $dateTime): self
    {
        $daysOfMonth = $this->getDaysOfMonth($dateTime);
        $this->presences = $this->getPresencesOfMonth($dateTime);
        $this->enfants = $this->getEnfantsPresentsOfMonth();

        $joursListing = [];

        foreach ($daysOfMonth as $jour) {
            $presences = $this->presenceRepository->findByDay($jour);
            $enfantsByday = array_map(
                function ($presence) {
                    return $presence->getEnfant();
                },
                $presences
            );
            $joursListing[] = new JourListing($jour, $enfantsByday);
        }
        $this->joursListing = $joursListing;

        return $this;
    }

    /**
     * @return Jour[]
     */
    public function getDaysOfMonth(DateTimeInterface $dateTime)
    {
        return $this->jourRepository->findDaysByMonth($dateTime);
    }

    /**
     * @return Presence[]
     */
    public function getPresences(): array
    {
        return $this->presences;
    }

    /**
     * @return Enfant[]
     */
    public function getEnfants(): array
    {
        return $this->enfants;
    }

    /**
     * @return JourListing[]
     */
    public function getJoursListing(): array
    {
        return $this->joursListing;
    }

    /**
     * @return Presence[]
     */
    private function getPresencesOfMonth(DateTimeInterface $dateTime): array
    {
        return $this->presenceRepository->findByMonth($dateTime);
    }

    /**
     * @return Enfant[]
     */
    private function getEnfantsPresentsOfMonth(): array
    {
        $enfants = array_map(
            function ($presence) {
                return $presence->getEnfant();
            },
            $this->presences
        );

        return array_unique($enfants);
    }
}
