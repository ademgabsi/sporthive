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
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Color\Color;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/gestion/match')]
final class GestionMatchController extends AbstractController
{
    private const QR_CODE_SIZE = 300;
    private const QR_CODE_MARGIN = 10;
    private $qrCodeBuilder;
    private $urlGenerator;

    public function __construct(BuilderInterface $qrCodeBuilder, UrlGeneratorInterface $urlGenerator)
    {
        $this->qrCodeBuilder = $qrCodeBuilder;
        $this->urlGenerator = $urlGenerator;
    }

    private function generateQrCodeForMatch(GestionMatch $gestionMatch): string
    {
        $url = $this->urlGenerator->generate('app_match_front_show', 
            ['id' => $gestionMatch->getId()], 
            UrlGeneratorInterface::ABSOLUTE_URL
        );

        $result = $this->qrCodeBuilder
            ->data($url)
            ->size(self::QR_CODE_SIZE)
            ->margin(self::QR_CODE_MARGIN)
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->foregroundColor(new Color(0, 0, 0))
            ->backgroundColor(new Color(255, 255, 255))
            ->build();

        return $result->getDataUri();
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
            $entityManager->persist($gestionMatch);
            $entityManager->flush();
            
            // Génération automatique du QR code
            $gestionMatch->setQrCode($this->generateQrCodeForMatch($gestionMatch));
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
            // Régénération automatique du QR code
            $gestionMatch->setQrCode($this->generateQrCodeForMatch($gestionMatch));
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
        if (!$this->isGranted('ROLE_ADMIN') && $gestionMatch->getUtilisateur() !== $this->getUser()) {
            throw new AccessDeniedException('Vous n\'avez pas accès à ce QR code.');
        }

        $result = $this->qrCodeBuilder
            ->data($this->urlGenerator->generate('app_match_front_show', 
                ['id' => $gestionMatch->getId()], 
                UrlGeneratorInterface::ABSOLUTE_URL
            ))
            ->size(self::QR_CODE_SIZE)
            ->margin(self::QR_CODE_MARGIN)
            ->errorCorrectionLevel(ErrorCorrectionLevel::High)
            ->build();
        
        return new QrCodeResponse($result);
    }

    #[Route('/{id}/generate-qr', name: 'app_gestion_match_generate_qr', methods: ['POST'])]
    public function generateQrCodeOnDemand(GestionMatch $gestionMatch, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN') && $gestionMatch->getUtilisateur() !== $this->getUser()) {
            throw new AccessDeniedException('Vous n\'avez pas le droit de générer ce QR code.');
        }

        $gestionMatch->setQrCode($this->generateQrCodeForMatch($gestionMatch));
        $entityManager->flush();
        
        return $this->redirectToRoute('app_gestion_match_show', ['id' => $gestionMatch->getId()]);
    }
}
