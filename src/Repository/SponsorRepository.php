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
        $conn = $this->getEntityManager()->getConnection();
        $sql = 'SELECT * FROM sponsor';
        $stmt = $conn->prepare($sql);
        $resultSet = $stmt->executeQuery();
        
        $sponsors = [];
        foreach ($resultSet->fetchAllAssociative() as $row) {
            $sponsor = new Sponsor();
            $sponsor->setId_Sp($row['id_Sp']);
            $sponsor->setNom_Sp($row['nom_Sp']);
            $sponsor->setType_Sp($row['type_Sp']);
            $sponsor->setMontantContrat($row['montantContrat']);
            
            if ($row['dateDebut']) {
                $dateDebut = new \DateTime($row['dateDebut']);
                $sponsor->setDateDebut($dateDebut);
            }
            
            if ($row['dateFin']) {
                $dateFin = new \DateTime($row['dateFin']);
                $sponsor->setDateFin($dateFin);
            }
            
            // Handle gestionMatch if needed
            
            $sponsors[] = $sponsor;
        }
        
        return $sponsors;
    }

//    public function findOneBySomeField($value): ?Sponsor
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
