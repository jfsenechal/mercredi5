<?php


namespace AcMarche\Mercredi\Relation\MessageHandler;

use AcMarche\Mercredi\Relation\Message\RelationDeleted;
use AcMarche\Mercredi\Relation\Repository\RelationRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RelationDeletedHandler implements MessageHandlerInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;
    /**
     * @var RelationRepository
     */
    private $relationRepository;

    public function __construct(RelationRepository $relationRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->relationRepository = $relationRepository;
    }

    public function __invoke(RelationDeleted $relationDeleted)
    {
        $this->flashBag->add('success', "La relation a bien été supprimée");
    }

}