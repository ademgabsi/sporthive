<?php
namespace App\Repository;

use App\Entity\Terrain;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

class TerrainRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Terrain::class);
    }

    // Méthode unifiée de recherche
   

    // Version avancée avec tri
    public function searchAndSort(?string $term, string $sortBy = 'idTerrain', string $direction = 'ASC'): array
    {
        $qb = $this->createSearchQueryBuilder($term);
        $this->applySorting($qb, $sortBy, $direction);

        return $qb->getQuery()->getResult();
    }

    // QueryBuilder de base pour les recherches
    private function createSearchQueryBuilder(?string $term): QueryBuilder
    {
        $qb = $this->createQueryBuilder('t');

        if ($term) {
            $qb->where('LOWER(t.nom) LIKE LOWER(:term)')
               ->orWhere('LOWER(t.localisation) LIKE LOWER(:term)')
               ->orWhere('t.prix = :numTerm')
               ->setParameter('term', '%' . $term . '%')
               ->setParameter('numTerm', is_numeric($term) ? (float)$term : null);
        }

        return $qb;
    }

    // Application du tri avec validation
    private function applySorting(QueryBuilder $qb, string $sortBy, string $direction): void
    {
        $allowedSorts = ['nom', 'prix', 'idTerrain'];
        $sortBy = in_array($sortBy, $allowedSorts, true) ? $sortBy : 'idTerrain';
        
        $direction = in_array(strtoupper($direction), ['ASC', 'DESC'], true) 
            ? strtoupper($direction) 
            : 'ASC';

        $qb->orderBy('t.' . $sortBy, $direction);
    }

    // Recherche pour l'API (version simplifiée)
    public function searchByQuery(string $query): array
{
    return $this->createQueryBuilder('t')
        ->where('LOWER(t.nom) LIKE LOWER(:query)')
        ->orWhere('LOWER(t.localisation) LIKE LOWER(:query)')
        ->orWhere('t.prix LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->setMaxResults(10)
        ->getQuery()
        ->getResult();
}
}