<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Traits\EnfantTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Validator as AcMarcheAssert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table("sante_fiche")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Sante\Repository\SanteFicheRepository")
 */
class SanteFiche implements TimestampableInterface
{
    use TimestampableTrait;

    use IdTrait,
        EnfantTrait,
        RemarqueTrait;

    /**
     * @ORM\Column(type="text", nullable=false)
     * @Assert\NotBlank()
     */
    protected $personne_urgence;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     */
    protected $medecin_nom;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=200, nullable=false)
     * @Assert\NotBlank()
     */
    protected $medecin_telephone;

    /**
     * @var Enfant
     * @ORM\OneToOne(targetEntity="AcMarche\Mercredi\Entity\Enfant")
     */
    protected $enfant;

    /**
     * @var SanteReponse[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteReponse", mappedBy="sante_fiche", cascade={"remove"})
     */
    protected $reponses;

    /**
     * @var SanteQuestion[]|ArrayCollection
     * @AcMarcheAssert\ResponseIsComplete()
     */
    protected $questions;

    public function __construct()
    {
        $this->reponses = new ArrayCollection();
        $this->questions = new ArrayCollection();
    }

    public function __toString()
    {
        return 'Fiche '.$this->id;
    }

    public function getPersonneUrgence(): ?string
    {
        return $this->personne_urgence;
    }

    public function setPersonneUrgence(string $personne_urgence): self
    {
        $this->personne_urgence = $personne_urgence;

        return $this;
    }

    public function getMedecinNom(): ?string
    {
        return $this->medecin_nom;
    }

    public function setMedecinNom(string $medecin_nom): self
    {
        $this->medecin_nom = $medecin_nom;

        return $this;
    }

    public function getMedecinTelephone(): ?string
    {
        return $this->medecin_telephone;
    }

    public function setMedecinTelephone(string $medecin_telephone): self
    {
        $this->medecin_telephone = $medecin_telephone;

        return $this;
    }

    /**
     * @return Collection|SanteReponse[]
     */
    public function getReponses(): Collection
    {
        return $this->reponses;
    }

    /**
     * @param SanteReponse[] $reponses
     */
    public function setReponses(array $reponses): void
    {
        $this->reponses = $reponses;
    }

    /**
     * @return SanteQuestion[]|ArrayCollection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * @param SanteQuestion[]|ArrayCollection $questions
     */
    public function setQuestions($questions): void
    {
        $this->questions = $questions;
    }

    public function addSanteQuestion(SanteQuestion $santeQuestion): self
    {
        if (!$this->questions->contains($santeQuestion)) {
            $this->questions[] = $santeQuestion;
            $santeQuestion->setSanteFiche($this);
        }

        return $this;
    }

    public function removeSanteQuestion(SanteQuestion $santeQuestion): self
    {
        if ($this->questions->contains($santeQuestion)) {
            $this->questions->removeElement($santeQuestion);
            // set the owning side to null (unless already changed)
            // if ($santeQuestion->getEnfant() === $this) {
            //   $santeQuestion->setEnfant(null);
            // }
        }

        return $this;
    }

    public function addReponse(SanteReponse $reponse): self
    {
        if (!$this->reponses->contains($reponse)) {
            $this->reponses[] = $reponse;
            $reponse->setSanteFiche($this);
        }

        return $this;
    }

    public function removeReponse(SanteReponse $reponse): self
    {
        if ($this->reponses->contains($reponse)) {
            $this->reponses->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getSanteFiche() === $this) {
                $reponse->setSanteFiche(null);
            }
        }

        return $this;
    }
}