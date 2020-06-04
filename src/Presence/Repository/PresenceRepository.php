<?php

namespace AcMarche\Mercredi\Presence\Repository;

use AcMarche\Mercredi\Entity\Ecole;
use AcMarche\Mercredi\Entity\Enfant;
use AcMarche\Mercredi\Entity\Jour;
use AcMarche\Mercredi\Entity\Presence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Presence|null find($id, $lockMode = null, $lockVersion = null)
 * @method Presence|null findOneBy(array $criteria, array $orderBy = null)
 * @method Presence[]    findAll()
 * @method Presence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PresenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Presence::class);
    }

    /**
     * @return Jour[]
     */
    public function findDaysRegisteredByEnfant(Enfant $enfant): array
    {
        $presences = $this->findPresencesByEnfant($enfant);
        $jours = [];
        foreach ($presences as $presence) {
            $jours[] = $presence->getJour();
        }

        return $jours;
    }

    /**
     * @return Presence[]
     */
    public function findPresencesByEnfant(Enfant $enfant): array
    {
        return $this->createQueryBuilder('presence')
            ->leftJoin('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->getQuery()->getResult();
    }

    /**
     * @return Presence
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function exist(Enfant $enfant, Jour $jour): ?Presence
    {
        return $this->createQueryBuilder('presence')
            ->andWhere('presence.enfant = :enfant')
            ->setParameter('enfant', $enfant)
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param \DateTimeInterface $date mm/YYYY
     *
     * @return Presence[]
     */
    public function findByMonth(\DateTimeInterface $date): array
    {
        $jours = $this->getEntityManager()->getRepository(Jour::class)->findDaysByMonth($date);

        return $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('presence.jour IN (:jours)')
            ->setParameter('jours', $jours)
            ->getQuery()->getResult();
    }

    /**
     * @param $jour
     *
     * @return Presence[]
     */
    public function findByDay($jour)
    {
        return $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant')
            ->andWhere('presence.jour = :jour')
            ->setParameter('jour', $jour)
            ->getQuery()->getResult();
    }

    /**
     * @param string $nom
     * @param Ecole  $ecole
     * @param string $annee_scolaire
     *
     * @return Presence[]
     */
    public function findPresencesByJourAndEcole(Jour $jour, ?Ecole $ecole): array
    {
        $qb = $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant');

        if ($jour) {
            $qb->andWhere('presence.jour = :jour')
                ->setParameter('jour', $jour);
        }

        if ($ecole) {
            $qb->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return Presence[]
     */
    public function search(string $nom, Ecole $ecole, string $annee_scolaire): array
    {
        $qb = $this->createQueryBuilder('presence')
            ->join('presence.enfant', 'enfant', 'WITH')
            ->addSelect('enfant');

        if ($nom) {
            $qb->andWhere('enfant.nom LIKE :nom')
                ->setParameter('nom', $nom);
        }

        if ($ecole) {
            $qb->andWhere('enfant.ecole = :ecole')
                ->setParameter('ecole', $ecole);
        }

        if ($annee_scolaire) {
            $qb->andWhere('enfant.annee_scolaire = :annee')
                ->setParameter('annee', $annee_scolaire);
        }

        return $qb->getQuery()->getResult();
    }

    public function remove(Presence $presence)
    {
        $this->_em->remove($presence);
    }

    public function flush()
    {
        $this->_em->flush();
    }

    public function persist(Presence $presence)
    {
        $this->_em->persist($presence);
    }
}
