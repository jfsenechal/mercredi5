<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\ContentTrait;
use AcMarche\Mercredi\Entity\Traits\DocumentsTraits;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;

/**
 * @ORM\Entity()
 */
class Page implements SluggableInterface
{
    /**
     * @var bool
     */
    public $system = false;
    use IdTrait;
    use NomTrait;
    use ContentTrait;
    use SluggableTrait;
    use DocumentsTraits;

    /**
     * @var string|null
     * @ORM\Column(type="text", length=100, nullable=true)
     */
    private $slug_system;

    /**
     * @var int|null
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $position;

    public function __construct()
    {
        $this->system = false;
    }

    public function __toString()
    {
        return $this->nom;
    }

    public function getSluggableFields(): array
    {
        return ['nom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getSlugSystem(): ?string
    {
        return $this->slug_system;
    }

    public function setSlugSystem(?string $slug_system): self
    {
        $this->slug_system = $slug_system;

        return $this;
    }
}
