<?php

namespace App\Repository;

use App\Entity\Assurance;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Assurance>
 *
 * @method Assurance|null find($id, $lockMode = null, $lockVersion = null)
 * @method Assurance|null findOneBy(array $criteria, array $orderBy = null)
 * @method Assurance[]    findAll()
 * @method Assurance[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssuranceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Assurance::class);
    }

    public function save(Assurance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Assurance $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
