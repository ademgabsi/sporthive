<?php

namespace App\Controller;

use App\Entity\GestionMatch;
use App\Repository\GestionMatchRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/match')]
class GestionMatchFrontController extends AbstractController
{
    #[Route('/', name: 'app_match_front_index', methods: ['GET'])]
    public function index(GestionMatchRepository $gestionMatchRepository): Response
    {
        return $this->render('Gestion_Match/Front/index.html.twig', [
            'matches' => $gestionMatchRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_match_front_show', methods: ['GET'])]
    public function show(GestionMatch $gestionMatch): Response
    {
        return $this->render('Gestion_Match/Front/show.html.twig', [
            'match' => $gestionMatch,
        ]);
    }
}
