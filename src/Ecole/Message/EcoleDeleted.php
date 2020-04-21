<?php

namespace AcMarche\Mercredi\Ecole\Message;

class EcoleDeleted
{
    /**
     * @var int
     */
    private $ecoleId;

    public function __construct(int $ecoleId)
    {
        $this->ecoleId = $ecoleId;
    }

    /**
     * @return int
     */
    public function getEcoleId(): int
    {
        return $this->ecoleId;
    }
}