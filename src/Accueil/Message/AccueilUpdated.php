<?php

namespace AcMarche\Mercredi\Accueil\Message;

final class AccueilUpdated
{
    /**
     * @var int
     */
    private $accueilId;

    public function __construct(int $accueilId)
    {
        $this->accueilId = $accueilId;
    }

    public function getAccueilId(): int
    {
        return $this->accueilId;
    }
}
