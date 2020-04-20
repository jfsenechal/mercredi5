<?php


namespace AcMarche\Mercredi\Message\Ecole;

class EcoleCreated
{
    /**
     * @var int
     */
    private $ecoleId;

    public function __construct(int $enfantId)
    {
        $this->ecoleId = $enfantId;
    }

    /**
     * @return int
     */
    public function getEcoleId(): int
    {
        return $this->ecoleId;
    }
}
