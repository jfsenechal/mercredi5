<?php

namespace AcMarche\Mercredi\Organisation\Traits;

use AcMarche\Mercredi\Entity\Organisation;
use AcMarche\Mercredi\Organisation\Repository\OrganisationRepository;

trait OrganisationPropertyInitTrait
{
    /**
     * @var OrganisationRepository
     */
    private $organisationRepository;
    /**
     * @var Organisation|null
     */
    private $organisation;

    public function __construct(
        OrganisationRepository $organisationRepository
    ) {
        $this->organisationRepository = $organisationRepository;
    }

    /**
     * @required
     */
    public function setorganisationRepository(OrganisationRepository $organisationRepository): void
    {
        $this->organisationRepository = $organisationRepository;
        $this->setOrganisation();
    }

    public function setOrganisation(): void
    {
        if ($this->organisationRepository !== null) {
            $this->organisation = $this->organisationRepository->getOrganisation();
        }
    }
}
