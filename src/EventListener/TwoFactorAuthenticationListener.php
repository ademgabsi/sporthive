<?php

namespace App\EventListener;

use App\Entity\Utilisateur;
use App\Service\GoogleAuthenticatorService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TwoFactorAuthenticationListener implements EventSubscriberInterface
{
    private $tokenStorage;
    private $authService;
    private $urlGenerator;
    private $excludedRoutes = [
        'app_login',
        'app_logout',
        'app_login_otp_verify',
        'app_login_verify_otp',
        'app_register',
        'app_register_otp_setup',
        'app_register_verify_otp',
        '_wdt',
        '_profiler',
        'app_forgot_password',
        'app_reset_password'
    ];

    public function __construct(
        TokenStorageInterface $tokenStorage,
        GoogleAuthenticatorService $authService,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authService = $authService;
        $this->urlGenerator = $urlGenerator;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 9], // Priorité élevée pour intercepter tôt
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        // Ne pas intercepter les routes exclues
        if (in_array($route, $this->excludedRoutes)) {
            return;
        }

        $token = $this->tokenStorage->getToken();
        if (!$token instanceof TokenInterface) {
            return;
        }

        $user = $token->getUser();
        if (!$user instanceof Utilisateur) {
            return;
        }

        // Vérifier si l'utilisateur a activé 2FA et si la vérification n'est pas terminée
        if ($user->getIsTwoFactorEnabled() && $user->getGoogleAuthSecret() !== null) {
            if (!$this->authService->isTwoFactorAuthenticationComplete()) {
                // Rediriger vers la page de vérification 2FA
                $url = $this->urlGenerator->generate('app_login_otp_verify');
                $event->setResponse(new RedirectResponse($url));
            }
        }
    }
}
