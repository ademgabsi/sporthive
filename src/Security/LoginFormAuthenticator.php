<?php

namespace App\Security;

use App\Entity\Utilisateur;
use App\Repository\UtilisateurRepository;
use App\Service\ReCaptchaValidator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\SecurityRequestAttributes;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

class LoginFormAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    public const LOGIN_ROUTE = 'app_login';

    public function __construct(
        private UrlGeneratorInterface $urlGenerator,
        private UtilisateurRepository $utilisateurRepository,
        private ReCaptchaValidator $reCaptchaValidator
    ) {
    }

    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');
        $recaptchaResponse = $request->request->get('g-recaptcha-response');

        // Vérifier le reCAPTCHA avant de procéder à l'authentification
        if (empty($recaptchaResponse)) {
            throw new CustomUserMessageAuthenticationException('Veuillez confirmer que vous n\'êtes pas un robot.');
        }

        // Valider la réponse reCAPTCHA
        if (!$this->reCaptchaValidator->verify($recaptchaResponse, $request->getClientIp())) {
            throw new CustomUserMessageAuthenticationException('La vérification reCAPTCHA a échoué. Veuillez réessayer.');
        }

        $request->getSession()->set(SecurityRequestAttributes::LAST_USERNAME, $email);

        return new Passport(
            new UserBadge($email, function (string $email) {
                $user = $this->utilisateurRepository->findOneBy(['email' => $email]);
                
                if (!$user) {
                    throw new CustomUserMessageAuthenticationException('Email introuvable.');
                }
                
                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge(),
            ]
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        $user = $token->getUser();
        
        // Vérifier si l'authentification à deux facteurs est requise
        if ($user instanceof Utilisateur && $user->getIsTwoFactorEnabled()) {
            // Rediriger vers la nouvelle page de vérification OTP
            return new RedirectResponse($this->urlGenerator->generate('app_login_otp_verify'));
        }
        
        // Redirect based on user role
        if ($user instanceof Utilisateur) {
            $roles = $user->getRoles();
            
            if (in_array('ROLE_ADMIN', $roles)) {
                return new RedirectResponse($this->urlGenerator->generate('app_admin_dashboard'));
            }
        }
        
        // Default redirect for ROLE_USER
        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate(self::LOGIN_ROUTE);
    }
}
