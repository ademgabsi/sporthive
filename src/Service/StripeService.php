<?php

namespace App\Service;

use App\Entity\Assurance;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
    private string $stripeSecretKey;
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        // Using the test key from the project configuration
        $this->stripeSecretKey = 'sk_test_51RIVjsP8NJBf8eJ3JYd28ulBpS38DzKVuCfY3nXAvMrZeaOv9k0POB4R2eWm9F4Lnh05w02hXe1Tiie4EUI9PYf100LwbUfq0f';
        $this->urlGenerator = $urlGenerator;
        
        // Initialize Stripe with the API key
        Stripe::setApiKey($this->stripeSecretKey);
    }

    /**
     * Create a Stripe checkout session for an insurance
     */
    public function createCheckoutSession(Assurance $assurance, int $prixTotal): Session
    {
        // Configurer les paramètres de la session Stripe
        $sessionParams = [
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Assurance ' . $assurance->getTypeDeCouverture(),
                        'description' => 'Durée: ' . $assurance->getDuree() . ' mois',
                    ],
                    'unit_amount' => $prixTotal, // Montant en centimes
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->urlGenerator->generate('app_stripe_success', [
                'id' => $assurance->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'cancel_url' => $this->urlGenerator->generate('app_stripe_cancel', [
                'id' => $assurance->getId()
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'metadata' => [
                'assurance_id' => $assurance->getId()
            ]
        ];
        
        // Créer la session Stripe
        $session = Session::create($sessionParams);
        
        // Vérifier que l'URL est bien générée
        if (!isset($session->url) || empty($session->url)) {
            throw new \Exception('L\'URL de redirection Stripe n\'a pas été générée correctement');
        }
        
        return $session;
    }

    /**
     * Handle Stripe webhook events
     */
    public function handleWebhookEvent(string $payload, ?string $sigHeader, ?string $endpointSecret): \Stripe\Event
    {
        return \Stripe\Webhook::constructEvent(
            $payload, $sigHeader, $endpointSecret
        );
    }
}
