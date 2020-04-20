<?php

namespace AcMarche\Mercredi\Message\Enfant;

class EnfantDeleted
{
    /**
     * @var int
     */
    private $enfantId;

    public function __construct(int $enfantId)
    {
        $this->enfantId = $enfantId;
    }

    /**
     * @return int
     */
    public function getEnfantId(): int
    {
        return $this->enfantId;
    }

}