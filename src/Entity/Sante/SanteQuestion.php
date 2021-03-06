<?php

namespace AcMarche\Mercredi\Entity\Sante;

use AcMarche\Mercredi\Entity\Presence;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table("sante_question")
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Sante\Repository\SanteQuestionRepository")
 */
class SanteQuestion
{
    use IdTrait;
    use NomTrait;
    use RemarqueTrait;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=200)
     */
    private $nom;

    /**
     * Information complementaire necessaire.
     *
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $complement = false;

    /**
     * Texte d'aide pour le complement.
     *
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $complement_label;

    /**
     * @var int|null
     * @ORM\Column(type="integer",nullable=true)
     */
    private $display_order;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Sante\SanteReponse", mappedBy="question", cascade={"remove"})
     */
    private $reponse;

    private $reponseTxt;

    private $remarque;

    public function __construct()
    {
        $this->reponse = new ArrayCollection();
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function isComplement(): bool
    {
        return $this->complement;
    }

    public function setComplement(bool $complement): void
    {
        $this->complement = $complement;
    }

    public function getComplementLabel(): ?string
    {
        return $this->complement_label;
    }

    public function setComplementLabel(?string $complement_label): void
    {
        $this->complement_label = $complement_label;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->display_order;
    }

    public function setDisplayOrder(?int $display_order): void
    {
        $this->display_order = $display_order;
    }

    public function getComplement(): ?bool
    {
        return $this->complement;
    }

    /**
     * @return mixed
     */
    public function getReponseTxt()
    {
        return $this->reponseTxt;
    }

    /**
     * @param mixed $reponseTxt
     */
    public function setReponseTxt($reponseTxt): void
    {
        $this->reponseTxt = $reponseTxt;
    }

    /**
     * @return Collection|SanteReponse[]
     */
    public function getReponse(): Collection
    {
        return $this->reponse;
    }

    public function addReponse(SanteReponse $reponse): self
    {
        if (! $this->reponse->contains($reponse)) {
            $this->reponse[] = $reponse;
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(SanteReponse $reponse): self
    {
        if ($this->reponse->contains($reponse)) {
            $this->reponse->removeElement($reponse);
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

        return $this;
    }
}
