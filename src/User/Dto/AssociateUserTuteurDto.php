<?php

namespace AcMarche\Mercredi\User\Dto;

use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;

class AssociateUserTuteurDto
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var Tuteur|null
     */
    private $tuteur;

    /**
     * @var bool
     */
    private $sendEmail = true;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @return Tuteur
     */
    public function getTuteur(): ?Tuteur
    {
        return $this->tuteur;
    }

    public function setTuteur(Tuteur $tuteur): void
    {
        $this->tuteur = $tuteur;
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
