<?php

namespace App\Repository;

use App\Entity\Sponsor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sponsor>
 *
 * @method Sponsor|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sponsor|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sponsor[]    findAll()
 * @method Sponsor[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SponsorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sponsor::class);
    }

    public function save(Sponsor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sponsor $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

   /**
    * @return Sponsor[] Returns an array of Sponsor objects
    */
    public function findAllSponsors(): array
    {
        return $this->createQueryBuilder('s')
            ->orderBy('s.nom_Sp', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Récupère les sponsors qui ont sponsorisé le plus de matchs
     * @return array Les sponsors avec le nombre de matchs sponsorisés et le montant total des contrats
     */
    public function findTopSponsors(int $limit = 5): array
    {
        return $this->createQueryBuilder('s')
            ->select('s.nom_Sp', 's.type_Sp', 
                    'COUNT(DISTINCT s.gestionMatch) as match_count',
                    'SUM(s.montantContrat) as montant_total')
            ->where('s.gestionMatch IS NOT NULL')
            ->groupBy('s.nom_Sp', 's.type_Sp')
            ->orderBy('match_count', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
