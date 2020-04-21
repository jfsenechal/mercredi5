<?php


namespace AcMarche\Mercredi\Tuteur\Message;

class TuteurCreated
{
    /**
     * @var int
     */
    private $tuteurId;

    public function __construct(int $tuteurId)
    {
        $this->tuteurId = $tuteurId;
    }

    /**
     * @return int
     */
    public function getTuteurId(): int
    {
        return $this->tuteurId;
    }

}