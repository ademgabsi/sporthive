<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Entity\AssuranceUtilisateur;
use App\Form\AssuranceType;
use App\Repository\AssuranceRepository;
use App\Repository\AssuranceUtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/assurance')]
class AssuranceController extends AbstractController
{
    #[Route('/', name: 'app_assurance_index', methods: ['GET'])]
    public function index(AssuranceRepository $assuranceRepository, AssuranceUtilisateurRepository $assuranceUtilisateurRepository): Response
    {
        $user = $this->getUser();
        $assurances = $assuranceRepository->findAll();
        $souscriptions = [];
        
        if ($user) {
            $souscriptions = $assuranceUtilisateurRepository->findBy(['utilisateur' => $user]);
        }
        
        return $this->render('Gestion_Assurances/front/index.html.twig', [
            'assurances' => $assurances,
            'souscriptions' => $souscriptions
        ]);
    }

    #[Route('/admin', name: 'app_assurance_admin', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function adminIndex(AssuranceRepository $assuranceRepository): Response
    {
        return $this->render('Gestion_Assurances/admin/index.html.twig', [
            'assurances' => $assuranceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_assurance_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $assurance = new Assurance();
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($assurance);
            $entityManager->flush();

            return $this->redirectToRoute('app_assurance_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Assurances/admin/new.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_contrat}', name: 'app_assurance_show', methods: ['GET'])]
    public function show(Assurance $assurance): Response
    {
        return $this->render('Gestion_Assurances/front/show.html.twig', [
            'assurance' => $assurance,
        ]);
    }

    #[Route('/{ID_contrat}/edit', name: 'app_assurance_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(Request $request, Assurance $assurance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AssuranceType::class, $assurance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_assurance_admin', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Assurances/admin/edit.html.twig', [
            'assurance' => $assurance,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_contrat}/subscribe', name: 'app_assurance_subscribe', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function subscribe(Assurance $assurance, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        // Vérifier si l'utilisateur n'est pas déjà inscrit
        $existingSubscription = $entityManager->getRepository(AssuranceUtilisateur::class)
            ->findOneBy(['utilisateur' => $user, 'assurance' => $assurance]);
            
        if ($existingSubscription) {
            $this->addFlash('error', 'Vous êtes déjà inscrit à cette assurance.');
            return $this->redirectToRoute('app_assurance_index');
        }
        
        $subscription = new AssuranceUtilisateur();
        $subscription->setUtilisateur($user);
        $subscription->setAssurance($assurance);
        
        $entityManager->persist($subscription);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_stripe_checkout', [
            'id' => $assurance->getID_contrat()
        ]);
    }
}
