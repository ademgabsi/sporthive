<?php

namespace App\Service;

use App\Entity\Utilisateur;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use OTPHP\TOTP;

class GoogleAuthenticatorService
{
    private $requestStack;
    private $params;
    private $sessionKey = 'two_factor_auth_complete';

    public function __construct(RequestStack $requestStack, ParameterBagInterface $params)
    {
        $this->requestStack = $requestStack;
        $this->params = $params;
    }

    /**
     * Génère un nouveau secret pour Google Authenticator
     */
    public function generateSecret(): string
    {
        $totp = TOTP::create();
        return $totp->getSecret();
    }

    /**
     * Génère l'URL du QR code pour Google Authenticator
     */
    public function getQrCodeUrl(Utilisateur $user, string $secret): string
    {
        // Créer l'instance TOTP avec le secret fourni
        $totp = TOTP::create($secret);
        $totp->setLabel($user->getEmail());
        
        // Nom de l'application qui apparaîtra dans Google Authenticator
        $totp->setIssuer('SportHive Admin');
        
        // Retourne l'URL du QR code
        return $totp->getProvisioningUri();
    }

    /**
     * Vérifie si le code fourni est valide pour le secret donné
     */
    public function verifyCode(string $secret, string $code): bool
    {
        if (empty($secret) || empty($code)) {
            return false;
        }

        // Créer l'instance TOTP avec le secret de l'utilisateur
        $totp = TOTP::create($secret);
        
        // Vérifier le code avec une fenêtre de tolérance de ±1 période (pour gérer les décalages d'horloge)
        return $totp->verify($code, null, 1);
    }

    /**
     * Marque l'authentification à deux facteurs comme terminée dans la session
     */
    public function setTwoFactorAuthenticationStatus(bool $status): void
    {
        $session = $this->requestStack->getSession();
        $session->set($this->sessionKey, $status);
    }

    /**
     * Vérifie si l'authentification à deux facteurs est terminée
     */
    public function isTwoFactorAuthenticationComplete(): bool
    {
        $session = $this->requestStack->getSession();
        return $session->get($this->sessionKey, false) === true;
    }

    /**
     * Réinitialise le statut de l'authentification à deux facteurs
     */
    public function resetTwoFactorAuthenticationStatus(): void
    {
        $session = $this->requestStack->getSession();
        $session->remove($this->sessionKey);
    }
}
