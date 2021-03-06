<?php

namespace AcMarche\Mercredi\Security\Voter;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use AcMarche\Mercredi\Security\MercrediSecurity;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * It grants or denies permissions for actions related to blog posts (such as
 * showing, editing and deleting posts).
 *
 * See http://symfony.com/doc/current/security/voters.html
 */
final class JourVoter extends Voter
{
    public const ADD = 'jour_new';
    public const SHOW = 'jour_show';
    public const EDIT = 'jour_edit';
    public const DELETE = 'jour_delete';

    /**
     * @var User
     */
    private $user;
    /**
     * @var Jour
     */
    private $jour;
    /**
     * @var Security
     */
    private $security;
    /**
     * @var Jour[]|ArrayCollection
     */
    private $jours;
    /**
     * @var Animateur|null
     */
    private $animateur;

    public function __construct(
        RelationRepository $relationRepository,
        Security $security
    ) {
        $this->security = $security;
        $this->relationRepository = $relationRepository;
    }

    protected function supports($attribute, $subject)
    {
        if ($subject && !$subject instanceof Jour) {
            return false;
        }

        return \in_array(
            $attribute,
            [self::ADD, self::SHOW, self::EDIT, self::DELETE],
            true
        );
    }

    protected function voteOnAttribute($attribute, $jour, TokenInterface $token)
    {
        $this->user = $token->getUser();
        $this->jour = $jour;

        if (!$this->user instanceof User) {
            return false;
        }

        if ($this->security->isGranted(MercrediSecurity::ROLE_ADMIN)) {
            return true;
        }

        if (!$this->security->isGranted(MercrediSecurity::ROLE_ANIMATEUR)) {
            return false;
        }

        if (!$this->checkJoursAnimateur()) {
            return false;
        }

        switch ($attribute) {
            case self::SHOW:
                return $this->canView();
            case self::ADD:
                return $this->canAdd();
            case self::EDIT:
                return $this->canEdit();
            case self::DELETE:
                return $this->canDelete();
        }

        return false;
    }

    private function canView(): bool
    {
        return $this->jours->contains($this->jour);
    }

    private function canEdit(): bool
    {
        return false;//not use
    }

    private function canAdd(): bool
    {
        return false;//not use
    }

    private function canDelete(): bool
    {
        return false;//only admin
    }

    /**
     * @return bool
     */
    private function checkJoursAnimateur(): bool
    {
        $this->animateur = $this->user->getAnimateur();

        if (!$this->animateur) {
            return false;
        }

        $this->jours = $this->animateur->getJours();

        if (count($this->jours) === 0) {
            return false;
        }

        return true;
    }


}
