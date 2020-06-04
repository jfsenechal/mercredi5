<?php

namespace AcMarche\Mercredi\Tuteur\Message;

class TuteurDeleted
{
    /**
     * @var int
     */
    private $tuteurId;

    public function __construct(int $tuteurId)
    {
        $this->tuteurId = $tuteurId;
    }

    public function getTuteurId(): int
    {
        return $this->tuteurId;
    }
}
