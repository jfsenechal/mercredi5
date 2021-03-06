<?php

namespace AcMarche\Mercredi\Entity\Plaine;

trait PlaineTrait
{
    /**
     * @var Plaine|null
     */
    private $plaine;

    public function getPlaine(): ?Plaine
    {
        return $this->plaine;
    }

    public function setPlaine(?Plaine $plaine): void
    {
        $this->plaine = $plaine;
    }
}
