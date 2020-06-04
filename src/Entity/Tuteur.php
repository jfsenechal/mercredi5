<?php

namespace AcMarche\Mercredi\Entity;

use AcMarche\Mercredi\Entity\Traits\AdresseTrait;
use AcMarche\Mercredi\Entity\Traits\ArchiveTrait;
use AcMarche\Mercredi\Entity\Traits\ConjointTrait;
use AcMarche\Mercredi\Entity\Traits\EmailTrait;
use AcMarche\Mercredi\Entity\Traits\IdTrait;
use AcMarche\Mercredi\Entity\Traits\NomTrait;
use AcMarche\Mercredi\Entity\Traits\PrenomTrait;
use AcMarche\Mercredi\Entity\Traits\RelationTrait;
use AcMarche\Mercredi\Entity\Traits\RemarqueTrait;
use AcMarche\Mercredi\Entity\Traits\SexeTrait;
use AcMarche\Mercredi\Entity\Traits\TelephonieTrait;
use AcMarche\Mercredi\Entity\Traits\UserAddTrait;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * @ORM\Entity(repositoryClass="AcMarche\Mercredi\Tuteur\Repository\TuteurRepository")
 */
class Tuteur implements SluggableInterface, TimestampableInterface
{
    use IdTrait;
    use NomTrait;
    use PrenomTrait;
    use AdresseTrait;
    use EmailTrait;
    use ConjointTrait;
    use RemarqueTrait;
    use SexeTrait;
    use TelephonieTrait;
    use SluggableTrait;
    use ArchiveTrait;
    use RelationTrait;
    use TimestampableTrait;
    use UserAddTrait;

    /**
     * @var Relation[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Relation", mappedBy="tuteur", cascade={"remove"})
     */
    private $relations;

    /**
     * J'ai mis la definition pour pouvoir mettre le cascade.
     *
     * @var Presence[]
     * @ORM\OneToMany(targetEntity="AcMarche\Mercredi\Entity\Presence", mappedBy="tuteur", cascade={"remove"})
     */
    private $presences;

    public function getSluggableFields(): array
    {
        return ['nom', 'prenom'];
    }

    public function shouldGenerateUniqueSlugs(): bool
    {
        return true;
    }

    public function __construct()
    {
        $this->relations = [];
    }

    public function __toString()
    {
        return mb_strtoupper($this->nom, 'UTF-8').' '.$this->prenom;
    }
}
