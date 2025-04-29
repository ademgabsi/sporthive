<?php

namespace App\Controller;

use App\Entity\GestionMatch;
use App\Form\GestionMatchType;
use App\Repository\GestionMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Endroid\QrCodeBundle\Response\QrCodeResponse;
use Endroid\QrCode\Builder\BuilderInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

#[Route('/gestion/match')]
final class GestionMatchController extends AbstractController{
    private BuilderInterface $qrCodeBuilder;
    
    public function __construct(BuilderInterface $qrCodeBuilder)
    {
        $this->qrCodeBuilder = $qrCodeBuilder;
    }
    
    #[Route(name: 'app_gestion_match_index', methods: ['GET'])]
    public function index(GestionMatchRepository $gestionMatchRepository): Response
    {
        return $this->render('Gestion_Match/Back/index.html.twig', [
            'gestion_matches' => $gestionMatchRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_gestion_match_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $gestionMatch = new GestionMatch();
        $form = $this->createForm(GestionMatchType::class, $gestionMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Définir une valeur par défaut pour le QR code
            $gestionMatch->setQrCode('sera_mis_a_jour_apres_creation');
            
            $entityManager->persist($gestionMatch);
            $entityManager->flush();
            
            // Générer le QR code après avoir obtenu l'ID
            $this->generateQrCode($gestionMatch, $entityManager);

            return $this->redirectToRoute('app_gestion_match_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Match/Back/new.html.twig', [
            'gestion_match' => $gestionMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestion_match_show', methods: ['GET'])]
    public function show(GestionMatch $gestionMatch): Response
    {
        return $this->render('Gestion_Match/Back/show.html.twig', [
            'gestion_match' => $gestionMatch,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_gestion_match_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, GestionMatch $gestionMatch, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GestionMatchType::class, $gestionMatch);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            
            // Mettre à jour le QR code
            $this->generateQrCode($gestionMatch, $entityManager);

            return $this->redirectToRoute('app_gestion_match_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Match/Back/edit.html.twig', [
            'gestion_match' => $gestionMatch,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_gestion_match_delete', methods: ['POST'])]
    public function delete(Request $request, GestionMatch $gestionMatch, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$gestionMatch->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($gestionMatch);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_gestion_match_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/{id}/qr-code', name: 'app_gestion_match_qrcode', methods: ['GET'])]
    public function qrCode(GestionMatch $gestionMatch): Response
    {
        // Générer l'URL pour le QR code (lien vers la page de détails du match côté FRONT)
        // Utilisez la route front pour afficher les détails du match
        $url = $this->generateUrl('app_match_front_show', ['id' => $gestionMatch->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        try {
            $result = $this->qrCodeBuilder
                ->data($url)
                ->size(300)
                ->margin(10)
                ->build();
                
            $response = new QrCodeResponse($result);
            $response->headers->set('Content-Type', 'image/png');
            return $response;
        } catch (\Exception $e) {
            // En cas d'erreur, retourner une image par défaut ou un message d'erreur
            return new Response('Erreur lors de la génération du QR code: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    
    private function generateQrCode(GestionMatch $gestionMatch, EntityManagerInterface $entityManager): void
    {
        // Générer l'URL pour le QR code
        $url = $this->generateUrl('app_gestion_match_qrcode', ['id' => $gestionMatch->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
        
        // Stocker l'URL du QR code dans l'entité
        $gestionMatch->setQrCode($url);
        $entityManager->flush();
    }
    
    #[Route('/test-qr-code', name: 'app_test_qr_code', methods: ['GET'])]
    public function testQrCode(): Response
    {
        return $this->render('Gestion_Match/Back/test_qr_code.html.twig');
    }
    
    #[Route('/calendrier', name: 'app_gestion_match_calendar', methods: ['GET'])]
    public function calendar(): Response
    {
        return $this->render('Gestion_Match/Front/calendar.html.twig');
    }
    
    #[Route('/api/events', name: 'app_gestion_match_events', methods: ['GET'])]
    public function getEvents(GestionMatchRepository $gestionMatchRepository): JsonResponse
    {
        $matches = $gestionMatchRepository->findAll();
        $events = [];
        
        foreach ($matches as $match) {
            // Combiner la date et l'heure pour obtenir un DateTime complet
            $startDate = clone $match->getDate();
            if ($match->getHeure()) {
                $startDate->setTime(
                    (int)$match->getHeure()->format('H'),
                    (int)$match->getHeure()->format('i')
                );
            }
            
            // Créer un événement pour FullCalendar
            $events[] = [
                'id' => $match->getId(),
                'title' => $match->getNom(),
                'start' => $startDate->format('Y-m-d\TH:i:s'),
                'end' => $startDate->modify('+2 hours')->format('Y-m-d\TH:i:s'), // Durée par défaut de 2 heures
                'description' => $match->getDescription(),
                'type' => $match->getType(),
                'url' => $this->generateUrl('app_match_front_show', ['id' => $match->getId()]),
                'backgroundColor' => $this->getColorForType($match->getType()),
                'borderColor' => $this->getColorForType($match->getType()),
            ];
        }
        
        return new JsonResponse($events);
    }
    
    /**
     * Retourne une couleur en fonction du type de match
     */
    private function getColorForType(string $type): string
    {
        return match ($type) {
            'football' => '#4caf50', // vert
            'basketball' => '#ff9800', // orange
            'volleyball' => '#2196f3', // bleu
            'handball' => '#f44336', // rouge
            default => '#9c27b0', // violet
        };
    }
}
