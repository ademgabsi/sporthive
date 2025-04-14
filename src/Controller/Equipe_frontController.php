<?php

namespace App\Controller;
use App\Entity\Joueur;
use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/front')]
final class Equipe_frontController extends AbstractController
{
#[Route('/', name: 'index_front', methods: ['GET'])]
    public function index_front(EquipeRepository $equipeRepository): Response
    {
        return $this->render('home/equipe_front.html.twig', [
            'equipes' => $equipeRepository->findAll(), 
        ]);
    }


    
#[Route('/joueur', name: 'index_front_joueur', methods: ['GET'])]
public function index_front_joueur(JoueurRepository $joueurRepository): Response
{
    return $this->render('home/joueur_front.html.twig', [
        'joueurs' => $joueurRepository->findAll(), 
    ]);
}

#[Route('/front/{id}', name: 'app_joueur_show_front', methods: ['GET'])]
    public function show(Joueur $joueur): Response
    {
        return $this->render('home/Profile_joueur_front.html.twig', [
            'joueur' => $joueur,
        ]);
    }

    #[Route('/frontEquipe/{id}', name: 'app_equipe_show_front', methods: ['GET'])]
    public function showEquipe(Equipe $equipe): Response
    {
        return $this->render('home/Profile_equipe.html.twig', [
            'equipe' => $equipe,
        ]);
    }
}