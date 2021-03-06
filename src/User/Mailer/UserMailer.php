<?php

namespace AcMarche\Mercredi\User\Mailer;

use AcMarche\Mercredi\Entity\Animateur;
use AcMarche\Mercredi\Entity\Security\User;
use AcMarche\Mercredi\Entity\Tuteur;
use AcMarche\Mercredi\Mailer\InitMailerTrait;
use AcMarche\Mercredi\Organisation\Traits\OrganisationPropertyInitTrait;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class UserMailer
{
    use InitMailerTrait;
    use OrganisationPropertyInitTrait;

    /**
     * @throws TransportExceptionInterface
     */
    public function sendNewAccountToTuteur(User $user, Tuteur $tuteur, ?string $password = null): void
    {
        $from = null !== $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        $templatedEmail = (new TemplatedEmail())
            ->subject('informations sur votre compte de '.$this->organisation->getNom())
            ->from($from)
            ->to($user->getEmail())
            ->htmlTemplate('@AcMarcheMercredi/front/mail/_mail_welcome_parent.html.twig')
            ->context(
                [
                    'tuteur' => $tuteur,
                    'user' => $user,
                    'password' => $password,
                    'organisation' => $this->organisation,
                ]
            );

        $this->sendMail($templatedEmail);
    }

    public function sendNewAccountToAnimateur(User $user, Animateur $animateur, ?string $password = null): void
    {
         $from = null !== $this->organisation ? $this->organisation->getEmail() : 'nomail@domain.be';

        $templatedEmail = (new TemplatedEmail())
            ->subject('informations sur votre compte de '.$this->organisation->getNom())
            ->from($from)
            ->to($user->getEmail())
            ->htmlTemplate('@AcMarcheMercredi/front/mail/_mail_welcome_animateur.html.twig')
            ->context(
                [
                    'animateur' => $animateur,
                    'user' => $user,
                    'password' => null,
                    'organisation' => $this->organisation,
                ]
            );

        $this->sendMail($templatedEmail);
    }


}
