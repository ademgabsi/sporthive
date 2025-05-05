<?php

namespace App\Controller;

use App\Entity\GestionMatch;
use App\Repository\GestionMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\ErrorCorrectionLevel;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/match')]
class GestionMatchFrontController extends AbstractController
{
    private $qrCodeBuilder;
    private $urlGenerator;
    private $entityManager;

    public function __construct(
        BuilderInterface $qrCodeBuilder,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager
    ) {
        $this->qrCodeBuilder = $qrCodeBuilder;
        $this->urlGenerator = $urlGenerator;
        $this->entityManager = $entityManager;
    }

    private function ensureQrCodeExists(GestionMatch $gestionMatch): void
    {
        if (!$gestionMatch->getQrCode()) {
            // Préparer les détails du match au format texte
            $matchDetails = json_encode([
                'nom' => $gestionMatch->getNom(),
                'type' => $gestionMatch->getType(),
                'date' => $gestionMatch->getDate() ? $gestionMatch->getDate()->format('d/m/Y') : 'Non définie',
                'heure' => $gestionMatch->getHeure() ? $gestionMatch->getHeure()->format('H:i') : 'Non définie',
                'description' => $gestionMatch->getDescription(),
            ], JSON_UNESCAPED_UNICODE);
            
            // Configuration optimisée pour la lecture mobile
            $result = $this->qrCodeBuilder
                ->data($matchDetails)
                ->size(300)
                ->margin(10)
                ->errorCorrectionLevel(ErrorCorrectionLevel::High)
                ->validateResult(false)
                ->build();
            
            $gestionMatch->setQrCode($result->getDataUri());
            $this->entityManager->flush();
        }
    }

    #[Route('/', name: 'app_match_front_index', methods: ['GET'])]
    public function index(GestionMatchRepository $gestionMatchRepository): Response
    {
        return $this->render('Gestion_Match/Front/index.html.twig', [
            'matches' => $gestionMatchRepository->findAll(),
        ]);
    }
    
    #[Route('/calendrier/view', name: 'app_match_front_calendar', methods: ['GET'])]
    public function calendar(): Response
    {
        return $this->render('Gestion_Match/Front/calendar.html.twig');
    }
    
    #[Route('/api/events', name: 'app_match_front_events', methods: ['GET'])]
    public function getEvents(GestionMatchRepository $gestionMatchRepository): JsonResponse
    {
        $matches = $gestionMatchRepository->findAll();
        $events = [];
        
        foreach ($matches as $match) {
            // Convertir DateTimeInterface en DateTime pour pouvoir utiliser setTime et modify
            $startDate = \DateTime::createFromInterface($match->getDate());
            if ($match->getHeure()) {
                $startDate->setTime(
                    (int)$match->getHeure()->format('H'),
                    (int)$match->getHeure()->format('i')
                );
            }
            
            // Calculer la date de fin (2 heures après le début)
            $endDate = clone $startDate;
            $endDate->modify('+2 hours');
            
            // Créer un événement pour FullCalendar
            $events[] = [
                'id' => $match->getId(),
                'title' => $match->getNom(),
                'start' => $startDate->format('Y-m-d\TH:i:s'),
                'end' => $endDate->format('Y-m-d\TH:i:s'),
                'extendedProps' => [
                    'description' => $match->getDescription(),
                    'type' => $match->getType()
                ],
                'url' => $this->generateUrl('app_match_front_show', ['id' => $match->getId()]),
                'backgroundColor' => $this->getColorForType($match->getType()),
                'borderColor' => $this->getColorForType($match->getType()),
                'allDay' => false
            ];
        }
        
        // Ajouter des en-têtes CORS pour permettre l'accès depuis n'importe quelle origine
        $response = new JsonResponse($events);
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');
        
        return $response;
    }

    #[Route('/{id}', name: 'app_match_front_show', methods: ['GET'])]
    public function show(GestionMatch $gestionMatch): Response
    {
        // Préparer les détails du match au format texte
        $matchDetails = $gestionMatch->getNom() . "\n" .
            $gestionMatch->getType() . "\n" .
            ($gestionMatch->getDate() ? $gestionMatch->getDate()->format('d/m/Y') : 'Date non définie') . "\n" .
            ($gestionMatch->getHeure() ? $gestionMatch->getHeure()->format('H:i') : 'Heure non définie') . "\n\n" .
            $gestionMatch->getDescription();
        
        // Générer le QR code
        $result = $this->qrCodeBuilder
            ->data($matchDetails)
            ->size(300)
            ->margin(10)
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->validateResult(false)
            ->build();
        
        // Sauvegarder le QR code dans l'entité
        $gestionMatch->setQrCode($result->getDataUri());
        $this->entityManager->flush();

        return $this->render('Gestion_Match/Front/show.html.twig', [
            'match' => $gestionMatch,
        ]);
    }

    private function getColorForType(string $type): string
    {
        return match ($type) {
            'Football' => '#2ecc71',
            'Basketball' => '#e74c3c',
            'Tennis' => '#f1c40f',
            'Volleyball' => '#3498db',
            default => '#95a5a6',
        };
    }
}
