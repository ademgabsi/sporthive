<?php

namespace App\Repository;

use App\Entity\Terrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class TerrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terrain::class);
    }
    public function search(string $term): array
{
    return $this->createQueryBuilder('t')
        ->where('t.nom LIKE :term') // <-- minuscules
        ->orWhere('t.localisation LIKE :term') 
        ->orWhere('t.prix LIKE :term')
        ->setParameter('term', '%' . $term . '%')
        ->getQuery()
        ->getResult();
}
public function searchAndSort(?string $term, string $sortBy = 'idTerrain', string $direction = 'ASC'): array
{
    $qb = $this->createQueryBuilder('t');

    if ($term) {
        $qb->where('t.nom LIKE :term')
           ->orWhere('t.localisation LIKE :term')
           ->orWhere('CAST(t.prix AS STRING) LIKE :term')
           ->setParameter('term', '%' . $term . '%');
    }

    // Validation des champs de tri
    $allowedSorts = ['nom', 'prix', 'idTerrain'];
    $sortBy = in_array($sortBy, $allowedSorts) ? $sortBy : 'idTerrain';
    
    $qb->orderBy('t.' . $sortBy, in_array(strtoupper($direction), ['ASC', 'DESC']) ? $direction : 'ASC');

    return $qb->getQuery()->getResult();
}  // Tu peux ajouter des méthodes personnalisées ici
}
