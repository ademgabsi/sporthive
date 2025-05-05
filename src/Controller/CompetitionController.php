<?php

namespace App\Controller;

use App\Entity\Competition;
use App\Form\CompetitionType;
use App\Repository\CompetitionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/competition')]
final class CompetitionController extends AbstractController{

   
    
    #[Route(name: 'app_competition_index', methods: ['GET'])]
    public function index(CompetitionRepository $competitionRepository): Response
    {
        return $this->render('competition/index.html.twig', [
            'competitions' => $competitionRepository->findAll(),
        ]);
    }

   

    #[Route('/{ID}', name: 'app_competition_show', methods: ['GET'])]
    public function show(Competition $competition): Response
    {
        return $this->render('competition/showFrontCompetition.html.twig', [
            'competition' => $competition,
        ]);
    }

    

  

    

}
