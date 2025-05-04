<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Service\GoogleAuthenticatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class RegistrationController extends AbstractController
{
    #[Route('/user/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        // Initialisation des variables
        $error = null;
        $user = new Utilisateur();
        
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $plainPassword = $request->request->get('password');
            $confirmPassword = $request->request->get('confirm_password');
            $firstName = $request->request->get('prenom');
            $lastName = $request->request->get('nom');
            $phoneNumber = $request->request->get('numero_tel');
            $address = $request->request->get('adresse');
            $birthDate = $request->request->get('date_naissance');

            // Vérifications de base
            if (empty($email) || empty($plainPassword) || empty($firstName) || empty($lastName)) {
                $error = 'Tous les champs marqués d\'un astérisque sont obligatoires.';
            } elseif ($plainPassword !== $confirmPassword) {
                $error = 'Les mots de passe ne correspondent pas.';
            } else {
                // Vérifier si l'email existe déjà
                $existingUser = $entityManager->getRepository(Utilisateur::class)->findOneBy(['email' => $email]);
                if ($existingUser) {
                    $error = 'Cet email est déjà utilisé.';
                } else {
                    // Stocker les données utilisateur en session pour l'étape OTP
                    $userData = [
                        'email' => $email,
                        'prenom' => $firstName,
                        'nom' => $lastName,
                        'numero_tel' => $phoneNumber,
                        'adresse' => $address,
                        'date_naissance' => $birthDate,
                        'password' => $passwordHasher->hashPassword($user, $plainPassword)
                    ];
                    
                    // Enregistrer les données en session
                    $session = $request->getSession();
                    $session->set('registration_data', $userData);
                    
                    // Rediriger vers l'étape de configuration OTP
                    return $this->redirectToRoute('app_register_otp_setup');
                }
            }
        }

        return $this->render('user/register.html.twig', [
            'error' => $error,
            'user' => $user
        ]);
    }
    
    /**
     * Page de vérification OTP après inscription
     */
    #[Route('/user/register/otp-setup', name: 'app_register_otp_setup')]
    public function registerOtpSetup(Request $request, GoogleAuthenticatorService $authService): Response
    {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $session = $request->getSession();
        $registrationData = $session->get('registration_data');
        
        // Si pas de données d'inscription en session, rediriger vers le formulaire d'inscription
        if (!$registrationData) {
            $this->addFlash('danger', 'Veuillez d\'abord compléter le formulaire d\'inscription.');
            return $this->redirectToRoute('app_register');
        }
        
        // Générer un secret pour Google Authenticator s'il n'existe pas déjà en session
        $secret = $session->get('registration_secret');
        if (!$secret) {
            $secret = $authService->generateSecret();
            $session->set('registration_secret', $secret);
        }
        
        // Créer un utilisateur temporaire pour générer le QR code
        $tempUser = new Utilisateur();
        $tempUser->setEmail($registrationData['email']);
        $qrCodeUrl = $authService->getQrCodeUrl($tempUser, $secret);
        
        try {
            // Générer le code QR
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new SvgImageBackEnd()
            );
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($qrCodeUrl);
        } catch (\Exception $e) {
            $qrCode = '<div class="alert alert-warning">Impossible de générer le code QR. Veuillez saisir la clé manuellement.</div>';
        }
        
        return $this->render('user/register_otp.html.twig', [
            'qrCode' => $qrCode,
            'secret' => $secret,
            'error' => null
        ]);
    }
    
    /**
     * Vérification du code OTP et création du compte
     */
    #[Route('/user/register/verify-otp', name: 'app_register_verify_otp', methods: ['POST'])]
    public function verifyRegisterOtp(
        Request $request, 
        GoogleAuthenticatorService $authService, 
        EntityManagerInterface $entityManager
    ): Response {
        // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }
        
        $session = $request->getSession();
        $registrationData = $session->get('registration_data');
        $secret = $session->get('registration_secret');
        $code = $request->request->get('code');
        
        // Vérifier que toutes les données nécessaires sont présentes
        if (!$registrationData || !$secret) {
            $this->addFlash('danger', 'Session expirée. Veuillez recommencer l\'inscription.');
            return $this->redirectToRoute('app_register');
        }
        
        // Vérifier le code OTP
        if (!$authService->verifyCode($secret, $code)) {
            // Recréer le QR code pour la page d'erreur
            try {
                $tempUser = new Utilisateur();
                $tempUser->setEmail($registrationData['email']);
                $qrCodeUrl = $authService->getQrCodeUrl($tempUser, $secret);
                
                $renderer = new ImageRenderer(
                    new RendererStyle(200),
                    new SvgImageBackEnd()
                );
                $writer = new Writer($renderer);
                $qrCode = $writer->writeString($qrCodeUrl);
            } catch (\Exception $e) {
                $qrCode = '<div class="alert alert-warning">Impossible de générer le code QR. Veuillez saisir la clé manuellement.</div>';
            }
            
            return $this->render('user/register_otp.html.twig', [
                'qrCode' => $qrCode,
                'secret' => $secret,
                'error' => 'Code de vérification invalide. Veuillez réessayer.'
            ]);
        }
        
        // Créer l'utilisateur avec les données stockées en session
        try {
            // Extraire les données du formulaire stockées en session
            $user = new Utilisateur();
            $user->setEmail($registrationData['email']);
            $user->setPrenom($registrationData['prenom']);
            $user->setNom($registrationData['nom']);
            $user->setNumeroTel($registrationData['numero_tel'] ?? null);
            $user->setAdresse($registrationData['adresse'] ?? null);
            
            if (isset($registrationData['date_naissance']) && !empty($registrationData['date_naissance'])) {
                $user->setDateNaissance(new \DateTime($registrationData['date_naissance']));
            }
            
            $user->setPassword($registrationData['password']);  // Déjà hashé dans le form handler
            
            // Configurer 2FA
            $user->setGoogleAuthSecret($secret);
            $user->setIsTwoFactorEnabled(true);
            $user->setIsActive(true);
            
            // Assigner le rôle utilisateur
            $roleRepo = $entityManager->getRepository(Role::class);
            $roleUser = $roleRepo->findOneBy(['nom' => 'utilisateur']);
            if ($roleUser) {
                $user->setRole($roleUser);
            }
            
            // Persister l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();
            
            // Nettoyer la session
            $session->remove('registration_data');
            $session->remove('registration_secret');
            
            // Message de succès et redirection
            $this->addFlash('success', 'Votre compte a été créé avec succès ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('app_login');
        } catch (\Exception $e) {
            return $this->render('user/register_otp.html.twig', [
                'qrCode' => $qrCode ?? null,
                'secret' => $secret,
                'error' => 'Une erreur est survenue lors de la création de votre compte : ' . $e->getMessage()
            ]);
        }
    }
}
