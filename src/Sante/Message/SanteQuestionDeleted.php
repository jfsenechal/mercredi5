<?php

namespace AcMarche\Mercredi\Sante\Message;

class SanteQuestionDeleted
{
    /**
     * @var int
     */
    private $santeQuestionId;

    public function __construct(int $santeQuestionId)
    {
        $this->santeQuestionId = $santeQuestionId;
    }

    public function getSanteQuestionId(): int
    {
        return $this->santeQuestionId;
    }
}
