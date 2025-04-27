<?php

namespace App\Controller;

use App\Service\AssuranceAnalyticsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

#[Route('/admin/assurance/analytics')]
class AssuranceAnalyticsController extends AbstractController
{
    private $analyticsService;
    private $chartBuilder;

    public function __construct(
        AssuranceAnalyticsService $analyticsService,
        ChartBuilderInterface $chartBuilder
    ) {
        $this->analyticsService = $analyticsService;
        $this->chartBuilder = $chartBuilder;
    }

    #[Route('/', name: 'app_admin_assurance_analytics')]
    public function index(): Response
    {
        // Récupérer les statistiques générales
        $generalStats = $this->analyticsService->getGeneralStats();
        
        // Créer le graphique de répartition par type d'assurance
        $typeChart = $this->createTypeChart();
        
        // Créer le graphique de répartition par statut
        $statusChart = $this->createStatusChart();
        
        // Créer le graphique d'évolution mensuelle
        $monthlyChart = $this->createMonthlyChart();
        
        // Créer le graphique de répartition par durée
        $durationChart = $this->createDurationChart();
        
        return $this->render('Gestion_Assurances/analytics/index_new.html.twig', [
            'generalStats' => $generalStats,
            'typeChart' => $typeChart,
            'statusChart' => $statusChart,
            'monthlyChart' => $monthlyChart,
            'durationChart' => $durationChart,
        ]);
    }
    
    private function createTypeChart(): Chart
    {
        $typeData = $this->analyticsService->getAssuranceTypeData();
        
        $chart = $this->chartBuilder->createChart(Chart::TYPE_PIE);
        $chart->setData([
            'labels' => $typeData['labels'],
            'datasets' => [
                [
                    'label' => 'Types d\'assurance',
                    'data' => $typeData['data'],
                    'backgroundColor' => $typeData['backgroundColor'],
                ],
            ],
        ]);
        
        $chart->setOptions([
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Répartition par type d\'assurance',
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ]);
        
        return $chart;
    }
    
    private function createStatusChart(): Chart
    {
        $statusData = $this->analyticsService->getAssuranceStatusData();
        
        $chart = $this->chartBuilder->createChart(Chart::TYPE_DOUGHNUT);
        $chart->setData([
            'labels' => $statusData['labels'],
            'datasets' => [
                [
                    'label' => 'Statuts d\'assurance',
                    'data' => $statusData['data'],
                    'backgroundColor' => $statusData['backgroundColor'],
                ],
            ],
        ]);
        
        $chart->setOptions([
            'responsive' => true,
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Répartition par statut d\'assurance',
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ]);
        
        return $chart;
    }
    
    private function createMonthlyChart(): Chart
    {
        $monthlyData = $this->analyticsService->getMonthlySubscriptionsData();
        
        $chart = $this->chartBuilder->createChart(Chart::TYPE_LINE);
        $chart->setData([
            'labels' => $monthlyData['labels'],
            'datasets' => [
                [
                    'label' => 'Souscriptions mensuelles',
                    'data' => $monthlyData['data'],
                    'backgroundColor' => 'rgba(75, 192, 192, 0.2)',
                    'borderColor' => 'rgba(75, 192, 192, 1)',
                    'borderWidth' => 2,
                    'tension' => 0.3,
                ],
            ],
        ]);
        
        $chart->setOptions([
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Évolution des souscriptions par mois',
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ]);
        
        return $chart;
    }
    
    private function createDurationChart(): Chart
    {
        $durationData = $this->analyticsService->getAssuranceDurationData();
        
        $chart = $this->chartBuilder->createChart(Chart::TYPE_BAR);
        $chart->setData([
            'labels' => $durationData['labels'],
            'datasets' => [
                [
                    'label' => 'Nombre d\'assurances',
                    'data' => $durationData['data'],
                    'backgroundColor' => 'rgba(54, 162, 235, 0.7)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ]);
        
        $chart->setOptions([
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'position' => 'top',
                ],
                'title' => [
                    'display' => true,
                    'text' => 'Répartition par durée d\'assurance',
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ]);
        
        return $chart;
    }
}
