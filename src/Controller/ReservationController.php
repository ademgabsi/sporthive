<?php

namespace App\Controller;
use App\Repository\TerrainRepository;

use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
#[Route('/reservation')]
final class ReservationController extends AbstractController
{
    #[Route(name: 'app_reservation_index', methods: ['GET'])]
    public function index(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }
    #[Route('/back', name: 'app_reservation_indexback', methods: ['GET'])]
    public function backindex(ReservationRepository $reservationRepository): Response
    {
        return $this->render('reservation/indexback.html.twig', [
            'reservations' => $reservationRepository->findAll(),
        ]);
    }

    #[Route('/new/{id_Terrain}', name: 'app_reservation_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request, 
        EntityManagerInterface $entityManager, 
        TerrainRepository $terrainRepository,
        ReservationRepository $reservationRepository,
        int $id_Terrain
    ): Response
    {
        // Récupérer le terrain
        $terrain = $terrainRepository->find($id_Terrain);
        
        if (!$terrain) {
            $this->addFlash('error', 'Terrain non trouvé.');
            return $this->redirectToRoute('app_terrain_index');
        }
    
        $reservation = new Reservation();
        $reservation->setTerrain($terrain);
    
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // 🔒 Vérifier si une réservation existe déjà à la même date/heure pour ce terrain
            $existing = $reservationRepository->findOneBy([
                'terrain' => $reservation->getTerrain(),
                'Date_Heure' => $reservation->getDateHeure()
            ]);
    
            if ($existing) {
                $form->get('Date_Heure')->addError(
                    new \Symfony\Component\Form\FormError('Ce terrain est déjà réservé à cette date et heure.')
                );
            } else {
                $entityManager->persist($reservation);
                $entityManager->flush();
    
                $this->addFlash('success', 'Réservation ajoutée avec succès.');
    
                return $this->redirectToRoute('app_reservation_index');
            }
        }
    
        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/admin/reservations/pdf', name: 'admin_reservation_pdf')]
public function generateReservationsPdf(ReservationRepository $reservationRepository, Request $request): Response
{
    // Récupération des paramètres
    $searchTerm = $request->query->get('q');
    $status = $request->query->get('status');
    $sortBy = $request->query->get('sort_by', 'dateReservation');
    $direction = $request->query->get('direction', 'DESC');

    // Récupération des réservations
    $reservations = $reservationRepository->searchAndSort(
        $searchTerm,
        $status,
        $sortBy,
        $direction
    );

    // Configuration PDF
    $pdfOptions = new Options();
    $pdfOptions->set('defaultFont', 'Arial');
    $pdfOptions->setIsRemoteEnabled(true);

    // Génération HTML
    $html = $this->renderView('admin/reservation/pdf.html.twig', [
        'reservations' => $reservations,
        'filters' => [
            'search' => $searchTerm,
            'status' => $status,
            'sort' => "$sortBy $direction"
        ]
    ]);

    // Génération PDF
    $dompdf = new Dompdf($pdfOptions);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    return new Response(
        $dompdf->stream("reservations_".date('Ymd-His').".pdf", ["Attachment" => true]),
        200,
        [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="reservations.pdf"'
        ]
    );
}

    #[Route('/{ID_Reservation}', name: 'app_reservation_show', methods: ['GET'])]
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }
    #[Route('/admin/reservation/{ID_Reservation}', name: 'app_reservation_showback', methods: ['GET'])]
    public function adminShow(Reservation $reservation): Response
    {
        return $this->render('reservation/showback.html.twig', [
            'reservation' => $reservation,
        ]);
    }



    #[Route('/{ID_Reservation}/edit', name: 'app_reservation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_Reservation}', name: 'app_reservation_delete', methods: ['POST'])]
    public function delete(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getID_Reservation(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reservation_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/admin/reservation/{ID_Reservation}/delete', name: 'app_reservation_deleteback', methods: ['POST'])]
public function deleteBack(Request $request, Reservation $reservation, EntityManagerInterface $entityManager): Response
{
    // Vérification du CSRF Token
    if ($this->isCsrfTokenValid('delete'.$reservation->getID_Reservation(), $request->get('_token'))) {
        // Suppression de la réservation
        $entityManager->remove($reservation);
        $entityManager->flush();

        // Message flash de succès
        $this->addFlash('success', 'Réservation supprimée avec succès.');
    } else {
        // Message flash si la vérification du CSRF échoue
        $this->addFlash('error', 'Erreur de suppression, veuillez réessayer.');
    }

    // Redirection vers la liste des réservations dans le back-office
    return $this->redirectToRoute('app_reservation_indexback');
}

}
