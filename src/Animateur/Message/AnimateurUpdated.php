<?php

namespace AcMarche\Mercredi\Animateur\Message;

class AnimateurUpdated
{
    /**
     * @var int
     */
    private $animateurId;

    public function __construct(int $animateurId)
    {
        $this->animateurId = $animateurId;
    }

    public function getAnimateurId(): int
    {
        return $this->animateurId;
    }
}
