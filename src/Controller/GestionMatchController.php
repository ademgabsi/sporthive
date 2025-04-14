<?php

namespace App\Controller;

use App\Entity\GestionMatch;
use App\Form\GestionMatchType;
use App\Repository\GestionMatchRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/gestion/match')]
final class GestionMatchController extends AbstractController{
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
            $entityManager->persist($gestionMatch);
            $entityManager->flush();

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
}
