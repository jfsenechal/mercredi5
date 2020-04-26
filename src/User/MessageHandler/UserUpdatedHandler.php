<?php


namespace AcMarche\Mercredi\User\MessageHandler;

use AcMarche\Mercredi\User\Message\UserUpdated;
use AcMarche\Mercredi\User\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UserUpdatedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->userRepository = $userRepository;
    }

    public function __invoke(UserUpdated $userUpdated)
    {
        $this->flashBag->add('success', "L'école a bien été modifiée");
    }

}