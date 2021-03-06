<?php

namespace AcMarche\Mercredi\Page\Repository;

use AcMarche\Mercredi\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 *                                                                                                 method Page[] findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class PageRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const SLUG_SYSTEM = 'slug_system';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Page::class);
    }

    /**
     * @return Page[]
     */
    public function findAll()
    {
        return $this->findBy([], ['position' => 'ASC']);
    }

    public function findHomePage(): ?Page
    {
        return $this->findOneBy([self::SLUG_SYSTEM => 'home']);
    }

    public function findContactPage(): ?Page
    {
        return $this->findOneBy([self::SLUG_SYSTEM => 'contact']);
    }

    public function findModalitePage(): ?Page
    {
        return $this->findOneBy([self::SLUG_SYSTEM => 'modalites-pratiques']);
    }

    public function remove(Page $page): void
    {
        $this->_em->remove($page);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Page $page): void
    {
        $this->_em->persist($page);
    }
}
