<?php

namespace AcMarche\Mercredi\Presence\Message;

final class PresenceCreated
{
    /**
     * @var array
     */
    private $days;

    public function __construct(array $days)
    {
        $this->days = $days;
    }

    public function getDays(): array
    {
        return $this->days;
    }
}
