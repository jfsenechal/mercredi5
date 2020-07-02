<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Scolaire\Repository\AnneeScolaireRepository")
 */
class AnneeScolaire
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @var Enfant[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Enfant", mappedBy="annee_scolaire")
     */
    private $enfants;

    /**
     * @var GroupeScolaire
     * @ORM\ManyToOne(targetEntity="AcMarche\Mercredi\Entity\GroupeScolaire", inversedBy="annees_scolaires")
     */
    private $groupe_scolaire;

    public function __construct()
    {
        $this->enfants = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    /**
     * @return Collection|Enfant[]
     */
    public function getEnfants(): Collection
    {
        return $this->enfants;
    }

    public function addEnfant(Enfant $enfant): self
    {
        if (!$this->enfants->contains($enfant)) {
            $this->enfants[] = $enfant;
            $enfant->setAnneeScolaire($this);
        }

        return $this;
    }

    public function removeEnfant(Enfant $enfant): self
    {
        if ($this->enfants->contains($enfant)) {
            $this->enfants->removeElement($enfant);
            // set the owning side to null (unless already changed)
            if ($enfant->getAnneeScolaire() === $this) {
                $enfant->setAnneeScolaire(null);
            }
        }

        return $this;
    }

    public function getGroupeScolaire(): ?GroupeScolaire
    {
        return $this->groupe_scolaire;
    }

    public function setGroupeScolaire(?GroupeScolaire $groupe_scolaire): self
    {
        $this->groupe_scolaire = $groupe_scolaire;

        return $this;
    }
}