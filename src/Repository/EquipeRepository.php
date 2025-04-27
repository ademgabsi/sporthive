<?php

namespace App\Repository;

use App\Entity\Equipe;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Equipe>
 *
 * @method Equipe|null find($id, $lockMode = null, $lockVersion = null)
 * @method Equipe|null findOneBy(array $criteria, array $orderBy = null)
 * @method Equipe[]    findAll()
 * @method Equipe[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EquipeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Equipe::class);
    }

    public function searchByNomOrVille(string $search): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.nom LIKE :search')
            ->orWhere('e.ville LIKE :search')
            ->setParameter('search', '%' . $search . '%')
            ->orderBy('e.nom', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
