<?php

namespace AcMarche\Mercredi\User\Dto;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Animateur;
use Symfony\Component\Security\Core\User\UserInterface;

final class AssociateUserAnimateurDto
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Animateur|null
     */
    private $animateur;

    /**
     * @var bool
     */
    private $sendEmail = true;

    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Animateur
     */
    public function getAnimateur(): ?Animateur
    {
        return $this->animateur;
    }

    public function setAnimateur(Animateur $tuteur): void
    {
        $this->animateur = $tuteur;
    }

    public function isSendEmail(): bool
    {
        return $this->sendEmail;
    }

    public function setSendEmail(bool $sendEmail): void
    {
        $this->sendEmail = $sendEmail;
    }
}
