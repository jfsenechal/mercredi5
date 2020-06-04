<?php

namespace AcMarche\Mercredi\Entity\Traits;

trait RemarqueTrait
{
    /**
     * @var string|null
     * @ORM\Column(type="text", nullable=true)
     */
    private $remarque;

    public function getRemarque(): ?string
    {
        return $this->remarque;
    }

    public function setRemarque(?string $remarque): void
    {
        $this->remarque = $remarque;
    }
}
