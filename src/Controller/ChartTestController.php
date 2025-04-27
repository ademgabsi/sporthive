<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\UX\Chartjs\Builder\ChartBuilderInterface;
use Symfony\UX\Chartjs\Model\Chart;

class ChartTestController extends AbstractController
{
    #[Route('/admin/chart-test', name: 'app_chart_test')]
    public function index(ChartBuilderInterface $chartBuilder): Response
    {
        // Créer un graphique de test simple
        $chart = $chartBuilder->createChart(Chart::TYPE_PIE);
        
        $chart->setData([
            'labels' => ['Rouge', 'Bleu', 'Jaune'],
            'datasets' => [
                [
                    'label' => 'Test',
                    'data' => [12, 19, 3],
                    'backgroundColor' => [
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
                    ],
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
                    'text' => 'Graphique de test',
                    'font' => [
                        'size' => 16,
                    ],
                ],
            ],
        ]);
        
        return $this->render('chart_test/index.html.twig', [
            'chart' => $chart,
        ]);
    }
}
