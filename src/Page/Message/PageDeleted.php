<?php

namespace AcMarche\Mercredi\Page\Message;

final class PageDeleted
{
    /**
     * @var int
     */
    private $pageId;

    public function __construct(int $pageId)
    {
        $this->pageId = $pageId;
    }

    public function getPageId(): int
    {
        return $this->pageId;
    }
}
