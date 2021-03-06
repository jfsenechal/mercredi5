<?php

namespace AcMarche\Mercredi\Organisation\Message;

final class OrganisationUpdated
{
    /**
     * @var int
     */
    private $organisationId;

    public function __construct(int $organisationId)
    {
        $this->organisationId = $organisationId;
    }

    public function getOrganisationId(): int
    {
        return $this->organisationId;
    }
}
