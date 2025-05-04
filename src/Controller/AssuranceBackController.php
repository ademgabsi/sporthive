<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Form\AssuranceType;
use App\Repository\AssuranceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/assurance')]
final class AssuranceBackController extends AbstractController
{
    #[Route('/', name: 'app_admin_assurance_index', methods: ['GET'])]
    public function index(Request $request, AssuranceRepository $assuranceRepository): Response
    {
        // Récupérer les paramètres de filtrage
        $searchTerm = $request->query->get('search', '');
        $statusFilter = $request->query->get('status', '');

        // Paramètres de pagination
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 5; // Nombre d'assurances par page
        $offset = ($page - 1) * $limit;

        // Récupérer toutes les assurances pour le filtrage
        $allAssurances = $assuranceRepository->findAll();

        // Filtrer les assurances selon les critères
        $filteredAssurances = $allAssurances;

        if (!empty($searchTerm)) {
            $filteredAssurances = array_filter($filteredAssurances, function($a) use ($searchTerm) {
                // Recherche dans l'ID, le type de couverture et le statut
                return
                    stripos((string)$a->getID_contrat(), $searchTerm) !== false ||
                    stripos($a->getTypeDeCouverture(), $searchTerm) !== false ||
                    stripos($a->getStatut(), $searchTerm) !== false;
            });
        }

        if (!empty($statusFilter)) {
            $filteredAssurances = array_filter($filteredAssurances, function($a) use ($statusFilter) {
                return $a->getStatut() === $statusFilter;
            });
        }

        // Trier les assurances par ID_contrat décroissant
        usort($filteredAssurances, function($a, $b) {
            return $b->getID_contrat() <=> $a->getID_contrat();
        });

        // Calculer le nombre total d'assurances filtrées et de pages
        $totalAssurances = count($filteredAssurances);
        $totalPages = ceil($totalAssurances / $limit);

        // Paginer les résultats filtrés
        $assurances = array_slice($filteredAssurances, $offset, $limit);

        // Calculer les statistiques
        $activeAssurances = count(array_filter($allAssurances, function($a) { return $a->getStatut() === 'Active'; }));
        $expiredAssurances = count(array_filter($allAssurances, function($a) { return $a->getStatut() === 'Expirée'; }));
        $pendingAssurances = count(array_filter($allAssurances, function($a) { return $a->getStatut() === 'En attente'; }));

        $stats = [
            'total' => count($allAssurances),
            'active' => $activeAssurances,
            'expired' => $expiredAssurances,
            'pending' => $pendingAssurances
        ];

        // Si c'est une requête AJAX, renvoyer le contenu de la table et les statistiques
        if ($request->isXmlHttpRequest()) {
            return $this->render('Gestion_Assurances/back/_assurance_list.html.twig', [
                'assurances' => $assurances,
                'page' => $page,
                'totalPages' => $totalPages,
                'stats' => $stats,
                'isAjax' => true,
                'searchTerm' => $searchTerm,
                'statusFilter' => $statusFilter
            ]);
        }

        // Sinon, renvoyer la page complète
        return $this->render('Gestion_Assurances/back/index.html.twig', [
            'assurances' => $assurances,
            'page' => $page,
            'totalPages' => $totalPages,
            'stats' => $stats,
            'searchTerm' => $searchTerm,
            'statusFilter' => $statusFilter
        ]);
    }

    #[Route('/new', name: 'app_admin_assurance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $assurance = new Assurance();
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($assurance);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_assurance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Assurances/back/new.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_contrat}', name: 'app_admin_assurance_show', methods: ['GET'])]
    public function show(Assurance $assurance): Response
    {
        return $this->render('Gestion_Assurances/back/show.html.twig', [
            'assurance' => $assurance,
        ]);
    }

    #[Route('/{ID_contrat}/edit', name: 'app_admin_assurance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Assurance $assurance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_assurance_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Assurances/back/edit.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_contrat}/delete', name: 'app_admin_assurance_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Assurance $assurance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete', $request->request->get('_token'))) {
            $entityManager->remove($assurance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_assurance_index', [], Response::HTTP_SEE_OTHER);
    }
}
