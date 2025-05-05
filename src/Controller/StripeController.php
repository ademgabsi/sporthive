<?php

namespace App\Controller;

use App\Entity\Assurance;
use App\Service\PricingService;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class StripeController extends AbstractController
{
    private $entityManager;
    private $stripeService;
    private $pricingService;

    public function __construct(
        EntityManagerInterface $entityManager,
        StripeService $stripeService,
        PricingService $pricingService
    ) {
        $this->entityManager = $entityManager;
        $this->stripeService = $stripeService;
        $this->pricingService = $pricingService;
    }

    #[Route('/paiement/assurance/{id}/form', name: 'app_payment_form')]
    public function paymentForm(Assurance $assurance): Response
    {
        return $this->render('paiement/form.html.twig', [
            'assurance' => $assurance
        ]);
    }

    #[Route('/paiement/assurance/{id}', name: 'app_stripe_checkout')]
    public function checkout(Assurance $assurance): Response
    {
        try {
            // Vérifier si l'assurance est déjà active
            if ($assurance->getStatut() === 'Active') {
                $this->addFlash('warning', 'Cette assurance est déjà active.');
                return $this->redirectToRoute('app_assurance_show', ['id' => $assurance->getId()]);
            }
            
            // Calculer le prix total via le service dédié
            $prixTotal = $this->pricingService->calculatePrice($assurance);
            
            // Créer la session de paiement via le service Stripe
            $session = $this->stripeService->createCheckoutSession($assurance, $prixTotal);
            
            // Vérifier que l'URL de redirection est bien disponible
            if (!isset($session->url) || empty($session->url)) {
                throw new \Exception('L\'URL de redirection Stripe n\'a pas été générée correctement');
            }
            
            // Rediriger vers la page de paiement Stripe
            return $this->redirect($session->url, 303);
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la création de la session de paiement: ' . $e->getMessage());
            return $this->redirectToRoute('app_payment_form', ['id' => $assurance->getId()]);
        }
    }

    #[Route('/paiement/success/{id}', name: 'app_stripe_success')]
    public function success(Assurance $assurance): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();
        
        // Lier l'utilisateur à l'assurance
        $assurance->setUtilisateur($user);
        
        // Marquer l'assurance comme payée
        $assurance->setIsPaid(true);
        
        // Si l'assurance est payée, la mettre en statut Active
        if ($assurance->getIsPaid()) {
            $assurance->setStatut('Active');
        } else {
            $assurance->setStatut('En attente');
        }
        
        $this->entityManager->flush();

        $this->addFlash('success', 'Paiement réussi ! Votre assurance est maintenant active.');
        
        return $this->render('paiement/success.html.twig', [
            'assurance' => $assurance
        ]);
    }

    #[Route('/paiement/cancel/{id}', name: 'app_stripe_cancel')]
    public function cancel(Assurance $assurance): Response
    {
        // S'assurer que l'assurance reste en attente si non payée
        if (!$assurance->getIsPaid()) {
            $assurance->setStatut('En attente');
            $this->entityManager->flush();
        }
        
        $this->addFlash('warning', 'Le paiement a été annulé.');
        
        return $this->render('paiement/cancel.html.twig', [
            'assurance' => $assurance
        ]);
    }

    #[Route('/paiement/webhook', name: 'app_stripe_webhook', methods: ['POST'])]
    public function webhook(Request $request): Response
    {
        // For webhook verification - you'll need to set this up in your Stripe dashboard
        $endpoint_secret = null; // Replace with your webhook secret when available
        $payload = $request->getContent();
        $sig_header = $request->headers->get('stripe-signature');
        $event = null;

        try {
            $event = $this->stripeService->handleWebhookEvent($payload, $sig_header, $endpoint_secret);
        } catch(\UnexpectedValueException $e) {
            return new Response('Webhook Error: ' . $e->getMessage(), 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Webhook Error: ' . $e->getMessage(), 400);
        }

        // Gérer l'événement
        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $assuranceId = $session->metadata->assurance_id;
                
                $assurance = $this->entityManager->getRepository(Assurance::class)->find($assuranceId);
                if ($assurance) {
                    $assurance->setStatut('Active');
                    $this->entityManager->flush();
                }
                break;
            default:
                // Gérer les autres types d'événements si nécessaire
        }

        return new Response('Webhook received', 200);
    }
}
