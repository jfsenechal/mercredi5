<?php

namespace AcMarche\Mercredi\Entity\Facture;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

trait FacturePresencesTrait
{
    /**
     * @var FacturePresence[]|ArrayCollection
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Facture\FacturePresence", mappedBy="facture", cascade={"remove"})
     */
    private $facturePresences;

    /**
     * @return Collection|FacturePresence[]
     */
    public function getFacturePresences(): Collection
    {
        return $this->facturePresences;
    }

    public function addFacturePresence(FacturePresence $facturePresence): self
    {
        if (! $this->facturePresences->contains($facturePresence)) {
            $this->facturePresences[] = $facturePresence;
            $facturePresence->setFacture($this);
        }

        return $this;
    }

    public function removeFacturePresence(FacturePresence $facturePresence): self
    {
        if ($this->facturePresences->contains($facturePresence)) {
            $this->facturePresences->removeElement($facturePresence);
            // set the owning side to null (unless already changed)
            if ($facturePresence->getFacture() === $this) {
                $facturePresence->setFacture(null);
            }
        }

        return $this;
    }
}
