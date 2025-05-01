<?php

namespace App\Controller;

use App\Entity\Sponsor;
use App\Repository\SponsorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sponsor')]
class SponsorFrontController extends AbstractController
{
    #[Route('/', name: 'app_sponsor_front_index', methods: ['GET'])]
    public function index(SponsorRepository $sponsorRepository): Response
    {
        return $this->render('Sponsor/Front/index.html.twig', [
            'sponsors' => $sponsorRepository->findAllSponsors(),
        ]);
    }

    #[Route('/{id_Sp}', name: 'app_sponsor_front_show', methods: ['GET'])]
    public function show(Sponsor $sponsor): Response
    {
        return $this->render('Sponsor/Front/show.html.twig', [
            'sponsor' => $sponsor,
        ]);
    }
}
