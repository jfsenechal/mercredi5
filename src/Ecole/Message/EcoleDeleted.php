<?php

namespace AcMarche\Mercredi\Ecole\Message;

final class EcoleDeleted
{
    /**
     * @var int
     */
    private $ecoleId;

    public function __construct(int $ecoleId)
    {
        $this->ecoleId = $ecoleId;
    }

    public function getEcoleId(): int
    {
        return $this->ecoleId;
    }
}
