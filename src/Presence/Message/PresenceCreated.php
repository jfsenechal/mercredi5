<?php

namespace AcMarche\Mercredi\Presence\Message;

class PresenceCreated
{
    /**
     * @var int
     */
    private $presenceId;

    public function __construct(int $presenceId)
    {
        $this->presenceId = $presenceId;
    }

    /**
     * @return int
     */
    public function getPresenceId(): int
    {
        return $this->presenceId;
    }
}