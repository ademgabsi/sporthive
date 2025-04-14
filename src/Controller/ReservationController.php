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
            int $id_Terrain
        ): Response
        {
            // Retrieve the terrain by ID
            $terrain = $terrainRepository->find($id_Terrain);
            
            // If the terrain is not found, redirect with an error
            if (!$terrain) {
                $this->addFlash('error', 'Terrain not found');
                return $this->redirectToRoute('app_terrain_index');
            }
        
            // Create a new Reservation and associate it with the terrain
            $reservation = new Reservation();
            $reservation->setTerrain($terrain);
        
            // Create the form for the reservation
            $form = $this->createForm(ReservationType::class, $reservation);
            $form->handleRequest($request);
        
            // Process form submission and validation
            if ($form->isSubmitted() && $form->isValid()) {
                // Persist the reservation in the database
                $entityManager->persist($reservation);
                $entityManager->flush();
        
                // Add a success flash message
                $this->addFlash('success', 'Reservation added successfully.');
        
                // Redirect to the reservation index page
                return $this->redirectToRoute('app_reservation_index');
            } else {
                // Add an error flash message if form is invalid
                $this->addFlash('error', 'Please correct the form errors.');
            }
        
            return $this->render('reservation/new.html.twig', [
                'reservation' => $reservation,
                'form' => $form->createView(),
            ]);
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
