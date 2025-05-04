<?php

namespace App\Controller;

use App\Service\GoogleAuthenticatorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Security;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, GoogleAuthenticatorService $authService): Response
    {
        // Si l'utilisateur est déjà connecté et a complété la vérification 2FA, rediriger vers la page d'accueil
        if ($this->getUser() && $authService->isTwoFactorAuthenticationComplete()) {
            if (in_array('ROLE_ADMIN', $this->getUser()->getRoles())) {
                return $this->redirectToRoute('app_admin_dashboard');
            }
            return $this->redirectToRoute('app_home');
        }
        
        // Si l'utilisateur est connecté mais n'a pas complété la vérification 2FA
        if ($this->getUser() && !$authService->isTwoFactorAuthenticationComplete()) {
            return $this->redirectToRoute('app_login_otp_verify');
        }

        // Obtenir l'erreur de connexion s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Dernier nom d'utilisateur entré
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }

    /**
     * Page de vérification OTP après connexion
     */
    #[Route('/login/otp-verify', name: 'app_login_otp_verify')]
    public function loginOtpVerify(Security $security): Response
    {
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('user/login_otp.html.twig', [
            'error' => null
        ]);
    }
    
    /**
     * Vérification du code OTP lors de la connexion
     */
    #[Route('/login/verify-otp', name: 'app_login_verify_otp', methods: ['POST'])]
    public function verifyLoginOtp(
        Request $request, 
        Security $security, 
        GoogleAuthenticatorService $authService
    ): Response {
        // Si l'utilisateur n'est pas connecté, rediriger vers la page de connexion
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        
        $user = $security->getUser();
        $code = $request->request->get('code');
        
        // Vérifier le code OTP
        if (!$authService->verifyCode($user->getGoogleAuthSecret(), $code)) {
            return $this->render('user/login_otp.html.twig', [
                'error' => 'Code de vérification invalide. Veuillez réessayer.'
            ]);
        }
        
        // Marquer l'authentification à deux facteurs comme complète
        $authService->setTwoFactorAuthenticationStatus(true);
        
        // Rediriger en fonction du rôle
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return $this->redirectToRoute('app_admin_dashboard');
        }
        
        return $this->redirectToRoute('app_home');
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(GoogleAuthenticatorService $authService): void
    {
        // Réinitialiser le statut de l'authentification à deux facteurs lors de la déconnexion
        $authService->resetTwoFactorAuthenticationStatus();
        
        // Le contrôleur ne sera jamais exécuté car la route est gérée par Symfony
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
