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
final class GestionMatchController extends AbstractController
{
    private $qrCodeBuilder;
    private $urlGenerator;

    public function __construct(BuilderInterface $qrCodeBuilder, UrlGeneratorInterface $urlGenerator)
    {
        $this->qrCodeBuilder = $qrCodeBuilder;
        $this->urlGenerator = $urlGenerator;
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
            // D'abord, on persiste l'entité pour obtenir son ID
            $entityManager->persist($gestionMatch);
            $entityManager->flush();
            
            // Ensuite, on génère le QR code avec l'ID maintenant disponible
            $url = $this->urlGenerator->generate('app_match_front_show', 
                ['id' => $gestionMatch->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            
            $result = $this->qrCodeBuilder
                ->data($url)
                ->size(300)
                ->build();
            
            // On met à jour le QR code et on sauvegarde à nouveau
            $gestionMatch->setQrCode($result->getDataUri());
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
            // Mettre à jour le QR code si nécessaire
            $url = $this->urlGenerator->generate('app_match_front_show', 
                ['id' => $gestionMatch->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            
            $result = $this->qrCodeBuilder
                ->data($url)
                ->size(300)
                ->build();
            
            $gestionMatch->setQrCode($result->getDataUri());
            
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

    #[Route('/{id}/qr-code', name: 'app_gestion_match_qr_code', methods: ['GET'])]
    public function generateQrCode(GestionMatch $gestionMatch): Response
    {
        $url = $this->urlGenerator->generate('app_match_front_show', 
            ['id' => $gestionMatch->getId()], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        $result = $this->qrCodeBuilder
            ->data($url)
            ->size(300)
            ->build();
        
        return new QrCodeResponse($result);
    }

    #[Route('/{id}/generate-qr', name: 'app_gestion_match_generate_qr', methods: ['POST'])]
    public function generateQrCodeOnDemand(GestionMatch $gestionMatch, EntityManagerInterface $entityManager): Response
    {
        $url = $this->urlGenerator->generate('app_match_front_show', 
            ['id' => $gestionMatch->getId()], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );
        
        $result = $this->qrCodeBuilder
            ->data($url)
            ->size(300)
            ->build();
        
        $gestionMatch->setQrCode($result->getDataUri());
        $entityManager->flush();
        
        return $this->redirectToRoute('app_gestion_match_show', ['id' => $gestionMatch->getId()]);
    }
}
