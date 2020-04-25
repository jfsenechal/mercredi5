<?php

namespace AcMarche\Mercredi\Relation\Message;

class RelationDeleted
{
    /**
     * @var int
     */
    private $ecoleId;

    public function __construct(int $relationId)
    {
        $this->ecoleId = $relationId;
    }

    /**
     * @return int
     */
    public function getRelationId(): int
    {
        return $this->ecoleId;
    }
}
