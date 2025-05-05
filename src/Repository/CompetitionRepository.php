<?php

namespace App\Repository;

use App\Entity\Competition;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Competition>
 *
 * @method Competition|null find($id, $lockMode = null, $lockVersion = null)
 * @method Competition|null findOneBy(array $criteria, array $orderBy = null)
 * @method Competition[]    findAll()
 * @method Competition[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompetitionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Competition::class);
    }

    // 🔍 Exemple : chercher les compétitions par type
    public function findByType(string $type): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Type = :type')
            ->setParameter('type', $type)
            ->orderBy('c.Date', 'DESC')
            ->getQuery()
            ->getResult();
    }

    // 🔍 Exemple : compétitions à venir
    public function findUpcoming(): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.Date >= :today')
            ->setParameter('today', new \DateTime())
            ->orderBy('c.Date', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
