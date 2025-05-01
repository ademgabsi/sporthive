<?php

namespace App\Service;

use App\Entity\Assurance;
use App\Entity\Reclamation;
use Doctrine\ORM\EntityManagerInterface;

class AssuranceAnalyticsService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Récupère les données pour le graphique de répartition des types d'assurance
     */
    public function getAssuranceTypeData(): array
    {
        $repository = $this->entityManager->getRepository(Assurance::class);
        
        // Vérifier s'il y a des assurances dans la base de données
        $count = $repository->createQueryBuilder('a')
            ->select('COUNT(a.ID_contrat)')
            ->getQuery()
            ->getSingleScalarResult();
            
        if ($count == 0) {
            // Retourner des données vides mais valides pour Chart.js
            return [
                'labels' => [],
                'data' => [],
                'backgroundColor' => [],
            ];
        }
        
        $typesData = $repository->createQueryBuilder('a')
            ->select('a.typeDeCouverture, COUNT(a.ID_contrat) as count')
            ->groupBy('a.typeDeCouverture')
            ->getQuery()
            ->getResult();
        
        $labels = [];
        $data = [];
        $backgroundColors = [
            'Basique' => 'rgba(54, 162, 235, 0.7)',
            'Standard' => 'rgba(75, 192, 192, 0.7)',
            'Premium' => 'rgba(255, 206, 86, 0.7)',
        ];
        $colors = [];
        
        foreach ($typesData as $typeData) {
            if (!empty($typeData['typeDeCouverture'])) {
                $labels[] = $typeData['typeDeCouverture'];
                $data[] = $typeData['count'];
                $colors[] = $backgroundColors[$typeData['typeDeCouverture']] ?? 'rgba(153, 102, 255, 0.7)';
            }
        }
        
        // Si aucune donnée valide n'a été trouvée, ajouter des données factices
        if (empty($labels)) {
            $labels = ['Aucune donnée'];
            $data = [0];
            $colors = ['rgba(200, 200, 200, 0.7)'];
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $colors,
        ];
    }
    
    /**
     * Récupère les données pour le graphique de répartition des statuts d'assurance
     */
    public function getAssuranceStatusData(): array
    {
        $repository = $this->entityManager->getRepository(Assurance::class);
        
        // Vérifier s'il y a des assurances dans la base de données
        $count = $repository->createQueryBuilder('a')
            ->select('COUNT(a.ID_contrat)')
            ->getQuery()
            ->getSingleScalarResult();
            
        if ($count == 0) {
            // Retourner des données vides mais valides pour Chart.js
            return [
                'labels' => [],
                'data' => [],
                'backgroundColor' => [],
            ];
        }
        
        $statusData = $repository->createQueryBuilder('a')
            ->select('a.Statut, COUNT(a.ID_contrat) as count')
            ->groupBy('a.Statut')
            ->getQuery()
            ->getResult();
        
        $labels = [];
        $data = [];
        $backgroundColors = [
            'Active' => 'rgba(75, 192, 192, 0.7)',
            'En attente' => 'rgba(255, 206, 86, 0.7)',
            'Expirée' => 'rgba(255, 99, 132, 0.7)',
        ];
        $colors = [];
        
        foreach ($statusData as $status) {
            if (!empty($status['Statut'])) {
                $labels[] = $status['Statut'];
                $data[] = $status['count'];
                $colors[] = $backgroundColors[$status['Statut']] ?? 'rgba(153, 102, 255, 0.7)';
            }
        }
        
        // Si aucune donnée valide n'a été trouvée, ajouter des données factices
        if (empty($labels)) {
            $labels = ['Aucune donnée'];
            $data = [0];
            $colors = ['rgba(200, 200, 200, 0.7)'];
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'backgroundColor' => $colors,
        ];
    }
    
    /**
     * Récupère les données pour le graphique d'évolution des souscriptions par mois
     */
    public function getMonthlySubscriptionsData(): array
    {
        $repository = $this->entityManager->getRepository(Assurance::class);
        
        // Vérifier s'il y a des assurances dans la base de données
        $count = $repository->createQueryBuilder('a')
            ->select('COUNT(a.ID_contrat)')
            ->getQuery()
            ->getSingleScalarResult();
            
        if ($count == 0) {
            // Définir les noms des mois
            $monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
            
            // Retourner des données vides mais valides pour Chart.js
            return [
                'labels' => $monthNames,
                'data' => array_fill(0, 12, 0),
            ];
        }
        
        // Comme dateDebut est une chaîne de caractères et non un objet DateTime,
        // nous allons récupérer toutes les assurances et traiter les dates en PHP
        $assurances = $repository->findAll();
        
        // Initialiser un tableau pour compter les assurances par mois
        $monthlyCount = array_fill(1, 12, 0);
        
        foreach ($assurances as $assurance) {
            $dateString = $assurance->getDateDebut();
            
            // Vérifier si la date est au format YYYY-MM-DD
            if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $dateString, $matches)) {
                $year = (int)$matches[1];
                $month = (int)$matches[2];
                
                // Si l'année correspond à l'année en cours, incrémenter le compteur pour ce mois
                if ($year === (int)date('Y')) {
                    $monthlyCount[$month]++;
                }
            }
        }
        
        $labels = [];
        $data = [];
        
        // Définir les noms des mois
        $monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
        
        // Construire les tableaux finaux
        foreach ($monthlyCount as $month => $count) {
            $labels[] = $monthNames[$month - 1];
            $data[] = $count;
        }
        
        // Vérifier si toutes les valeurs sont à zéro
        $hasData = false;
        foreach ($data as $value) {
            if ($value > 0) {
                $hasData = true;
                break;
            }
        }
        
        // Si toutes les valeurs sont à zéro, ajouter au moins une valeur non nulle pour que le graphique s'affiche
        if (!$hasData) {
            // Ajouter une valeur factice pour le mois en cours
            $currentMonth = (int)date('n') - 1; // 0-indexed
            $data[$currentMonth] = 1;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Récupère les données pour le graphique de répartition des durées d'assurance
     */
    public function getAssuranceDurationData(): array
    {
        $repository = $this->entityManager->getRepository(Assurance::class);
        
        // Vérifier s'il y a des assurances dans la base de données
        $count = $repository->createQueryBuilder('a')
            ->select('COUNT(a.ID_contrat)')
            ->getQuery()
            ->getSingleScalarResult();
            
        if ($count == 0) {
            // Retourner des données vides mais valides pour Chart.js
            return [
                'labels' => [],
                'data' => [],
            ];
        }
        
        $durationData = $repository->createQueryBuilder('a')
            ->select('a.Duree, COUNT(a.ID_contrat) as count')
            ->groupBy('a.Duree')
            ->orderBy('a.Duree', 'ASC')
            ->getQuery()
            ->getResult();
        
        $labels = [];
        $data = [];
        
        foreach ($durationData as $duration) {
            if (isset($duration['Duree']) && $duration['Duree'] !== null) {
                $labels[] = $duration['Duree'] . ' mois';
                $data[] = $duration['count'];
            }
        }
        
        // Si aucune donnée valide n'a été trouvée, ajouter des données factices
        if (empty($labels)) {
            $labels = ['6 mois', '12 mois', '24 mois'];
            $data = [0, 0, 0];
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }
    
    /**
     * Récupère les statistiques générales sur les assurances
     */
    public function getGeneralStats(): array
    {
        $repository = $this->entityManager->getRepository(Assurance::class);
        $reclamationRepository = $this->entityManager->getRepository(Reclamation::class);
        
        // Nombre total d'assurances
        $totalAssurances = $repository->createQueryBuilder('a')
            ->select('COUNT(a.ID_contrat)')
            ->getQuery()
            ->getSingleScalarResult();
        
        // Nombre d'assurances actives
        $activeAssurances = $repository->createQueryBuilder('a')
            ->select('COUNT(a.ID_contrat)')
            ->where('a.Statut = :status')
            ->setParameter('status', 'Active')
            ->getQuery()
            ->getSingleScalarResult();
        
        // Nombre d'assurances par type de couverture
        $assurancesByType = $repository->createQueryBuilder('a')
            ->select('a.typeDeCouverture, COUNT(a.ID_contrat) as count')
            ->groupBy('a.typeDeCouverture')
            ->getQuery()
            ->getResult();
        
        // Durée moyenne des assurances
        $avgDuration = $repository->createQueryBuilder('a')
            ->select('AVG(a.Duree)')
            ->getQuery()
            ->getSingleScalarResult();
        
        // Nombre total de réclamations
        $totalReclamations = $reclamationRepository->createQueryBuilder('r')
            ->select('COUNT(r.ID_reclamation)')
            ->getQuery()
            ->getSingleScalarResult();
        
        // Formater les données par type
        $typeData = [];
        foreach ($assurancesByType as $type) {
            $typeData[$type['typeDeCouverture']] = $type['count'];
        }
        
        return [
            'totalAssurances' => $totalAssurances ?: 0,
            'activeAssurances' => $activeAssurances ?: 0,
            'avgDuration' => round($avgDuration ?: 0, 1),
            'totalReclamations' => $totalReclamations ?: 0,
            'assurancesByType' => $typeData
        ];
    }
}
