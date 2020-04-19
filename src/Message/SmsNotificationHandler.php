<?php


namespace AcMarche\Mercredi\Message;

use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SmsNotificationHandler implements MessageHandlerInterface
{
    public function __invoke(SmsNotification $message)
    {
        dump($message->getContent());
    }
}
