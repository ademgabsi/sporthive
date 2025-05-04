<?php

namespace App\Repository;

use App\Entity\GestionMatch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<GestionMatch>
 *
 * @method GestionMatch|null find($id, $lockMode = null, $lockVersion = null)
 * @method GestionMatch|null findOneBy(array $criteria, array $orderBy = null)
 * @method GestionMatch[]    findAll()
 * @method GestionMatch[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class GestionMatchRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, GestionMatch::class);
    }

    public function save(GestionMatch $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(GestionMatch $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
