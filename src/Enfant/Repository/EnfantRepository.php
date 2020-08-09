<?php

namespace AcMarche\Mercredi\Enfant\Repository;

use AcMarche\Mercredi\Entity\AnneeScolaire;
use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Enfant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Enfant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Enfant[]    findAll()
 * @method Enfant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
final class EnfantRepository extends ServiceEntityRepository
{
    /**
     * @var string
     */
    private const ENFANT = 'enfant';
    /**
     * @var string
     */
    private const ASC = 'ASC';
    /**
     * @var string
     */
    private const ECOLE = 'ecole';
    /**
     * @var string
     */
    private const WITH = 'WITH';

    public function __construct(ManagerRegistry $managerRegistry)
    {
        parent::__construct($managerRegistry, Enfant::class);
    }

    /**
     * @return Enfant[]
     */
    public function findAllActif(): array
    {
        return $this->createQueryBuilder(self::ENFANT)
            ->andWhere('enfant.archived = 0')
            ->addOrderBy('enfant.nom', self::ASC)
            ->getQuery()->getResult();
    }

    /**
     * @param $keyword
     *
     * @return Enfant[]
     */
    public function findByName(string $keyword, bool $actif = true): array
    {
        $queryBuilder = $this->createQueryBuilder(self::ENFANT)
            ->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
            ->setParameter('keyword', '%'.$keyword.'%');

        if ($actif) {
            $queryBuilder->andWhere('enfant.archived = 0');
        }

        return $queryBuilder->addOrderBy('enfant.nom', self::ASC)
            ->getQuery()->getResult();
    }

    /**
     * @return Enfant[]
     */
    public function findOrphelins()
    {
        return $this->createQueryBuilder(self::ENFANT)
            ->andWhere('enfant.relations IS EMPTY')
            ->getQuery()->getResult();
    }

    /**
     * @return Enfant[]
     */
    public function search(?string $nom, ?Ecole $ecole, ?AnneeScolaire $anneeScolaire, bool $archive = false): array
    {
        $queryBuilder = $this->createQueryBuilder(self::ENFANT)
            ->leftJoin('enfant.ecole', self::ECOLE, self::WITH)
            ->leftJoin('enfant.annee_scolaire', 'annee_scolaire', self::WITH)
            ->leftJoin('enfant.sante_fiche', 'sante_fiche', self::WITH)
            ->leftJoin('enfant.relations', 'relations', self::WITH)
            ->addSelect(self::ECOLE, 'relations', 'sante_fiche', 'annee_scolaire');

        if ($nom) {
            $queryBuilder->andWhere('enfant.nom LIKE :keyword OR enfant.prenom LIKE :keyword')
                ->setParameter('keyword', '%'.$nom.'%');
        }

        if (null !== $ecole) {
            $queryBuilder->andWhere('ecole = :ecole')
                ->setParameter(self::ECOLE, $ecole);
        }

        if ($anneeScolaire) {
            $queryBuilder->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $anneeScolaire);
        }

        if ($archive) {
            $queryBuilder->andWhere('enfant.archived = 1');
        } else {
            $queryBuilder->andWhere('enfant.archived = 0');
        }

        return $queryBuilder->addOrderBy('enfant.nom', self::ASC)
            ->getQuery()->getResult();
    }

    public function remove(Enfant $enfant): void
    {
        $this->_em->remove($enfant);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    public function persist(Enfant $enfant): void
    {
        $this->_em->persist($enfant);
    }
}
