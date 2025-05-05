<?php

namespace App\Repository;

use App\Entity\Tournoi;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tournoi>
 *
 * @method Tournoi|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tournoi|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tournoi[]    findAll()
 * @method Tournoi[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TournoiRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tournoi::class);
    }

    // Exemple de méthode personnalisée : récupérer les tournois d’une compétition donnée
    public function findByCompetitionId(int $competitionId): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.competition = :val')
            ->setParameter('val', $competitionId)
            ->getQuery()
            ->getResult();
    }

    // Exemple : trouver les tournois à venir
    public function findUpcomingTournois(): array
    {
        return $this->createQueryBuilder('t')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();
    }
}
