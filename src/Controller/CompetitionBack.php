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

#[Route('/competitionBack')]
final class CompetitionBack extends AbstractController
{
    #[Route(name: 'app_competition_indexBack', methods: ['GET'])]
    public function index(CompetitionRepository $competitionRepository): Response
    {
        return $this->render('competition/getallcompetBack.html.twig', [
            'competitions' => $competitionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_competition_newBack', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $competition = new Competition();
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($competition);
            $entityManager->flush();

            return $this->redirectToRoute('app_competition_indexBack', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('competition/new.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{ID}', name: 'app_competition_showBack', methods: ['GET'])]
    public function show(Competition $competition): Response
    {
        return $this->render('competition/show.html.twig', [
            'competition' => $competition,
        ]);
    }

    #[Route('/{ID}/edit', name: 'app_competition_editBack', methods: ['GET', 'POST'])]
    public function edit(Request $request, Competition $competition, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CompetitionType::class, $competition);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_competition_indexBack', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('competition/edit.html.twig', [
            'competition' => $competition,
            'form' => $form,
        ]);
    }

    #[Route('/{ID}', name: 'app_competition_deleteBack', methods: ['POST'])]
    public function delete(Request $request, Competition $competition, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$competition->getID(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($competition);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_competition_indexBack', [], Response::HTTP_SEE_OTHER);
    }


    
}
