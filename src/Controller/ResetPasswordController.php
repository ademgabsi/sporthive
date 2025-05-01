<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Form\ResetPasswordRequestFormType;
use App\Form\ResetPasswordFormType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    private $entityManager;
    private $mailer;
    private $tokenGenerator;
    private $translator;
    private $logger;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        TokenGeneratorInterface $tokenGenerator,
        TranslatorInterface $translator,
        LoggerInterface $logger
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->tokenGenerator = $tokenGenerator;
        $this->translator = $translator;
        $this->logger = $logger;
    }

    /**
     * Génère un code aléatoire à 6 chiffres
     */
    private function generateRandomCode(): string
    {
        return str_pad(strval(random_int(0, 999999)), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Affiche et traite le formulaire de demande de réinitialisation
     */
    #[Route('/request', name: 'app_reset_password_request')]
    public function request(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            
            if (!$email) {
                $this->addFlash('danger', 'Veuillez fournir une adresse email.');
                return $this->render('user/forgot_password.html.twig');
            }
            
            $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([
                'email' => $email,
            ]);
            
            // Ne pas révéler si l'utilisateur existe ou non pour des raisons de sécurité
            if ($user) {
                // Générer un token pour le lien (identification unique de la demande)
                $token = $this->tokenGenerator->generateToken();
                $user->setResetToken($token);
                
                // Générer un code à 6 chiffres pour la vérification
                $resetCode = $this->generateRandomCode();
                $user->setResetCode($resetCode);
                
                // Date d'expiration (1 heure)
                $user->setResetTokenExpiresAt(new \DateTime('+1 hour'));
                
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                
                // URL pour la vérification du code
                $verifyUrl = $this->generateUrl(
                    'app_verify_reset_code', 
                    ['token' => $token], 
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                
                // Simplification de l'envoi d'email
                try {
                    // Envoyer l'email directement sans les logs détaillés
                    $email = (new TemplatedEmail())
                        ->from(new Address('support@sporthive.com', 'Sporthive Support'))
                        ->to($user->getEmail())
                        ->subject('Votre code de réinitialisation de mot de passe')
                        ->htmlTemplate('emails/reset_password_code.html.twig')
                        ->context([
                            'verifyUrl' => $verifyUrl,
                            'tokenLifetime' => '1 heure',
                            'user' => $user,
                            'resetCode' => $resetCode
                        ]);
                    
                    $this->mailer->send($email);
                    $this->logger->info('Email de réinitialisation envoyé à ' . $user->getEmail());
                } catch (\Exception $e) {
                    // Journalisation simplifiée des erreurs
                    $this->logger->error('Erreur d\'envoi email: ' . $e->getMessage());
                    
                    // Pour les tests : en cas d'échec d'envoi d'email, stocker le code en session
                    // IMPORTANT: NE PAS UTILISER EN PRODUCTION
                    $request->getSession()->set('temp_reset_code', [
                        'code' => $resetCode,
                        'email' => $user->getEmail(),
                        'token' => $token
                    ]);
                }
            } else {
                // Logging si l'email n'existe pas (pour le débogage uniquement)
                $this->logger->info('Tentative de réinitialisation pour un email inexistant: ' . $email);
                
                // Ajouter un délai aléatoire pour éviter les attaques par timing (révélant si un email existe ou non)
                usleep(random_int(100000, 300000)); // 100-300ms
            }
            
            // Pour des raisons de sécurité, afficher toujours le même message, que l'utilisateur existe ou non
            $this->addFlash(
                'success',
                'Si un compte existe avec cette adresse email, un code de réinitialisation vous a été envoyé. Veuillez vérifier votre boîte de réception et vos dossiers spam/indésirables.'
            );
            
            // Rediriger vers la page de saisie du code
            if ($user) {
                // Si l'utilisateur existe, on redirige vers la page de vérification avec le token
                return $this->redirectToRoute('app_verify_reset_code', ['token' => $token]);
            } else {
                // Si l'utilisateur n'existe pas, créer un faux token pour ne pas révéler l'inexistence
                $fakeToken = $this->tokenGenerator->generateToken();
                return $this->redirectToRoute('app_verify_reset_code', ['token' => $fakeToken]);
            }
        }
        
        return $this->render('user/forgot_password.html.twig');
    }
    
    /**
     * Page de saisie du code de vérification envoyé par email
     */
    #[Route('/verify/{token}', name: 'app_verify_reset_code')]
    public function verifyCode(Request $request, string $token = null): Response
    {
        // Vérifier que le token est valide
        if (!$token) {
            $this->addFlash('danger', 'Aucun token de réinitialisation trouvé.');
            return $this->redirectToRoute('app_reset_password_request');
        }
        
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([
            'resetToken' => $token
        ]);
        
        if (null === $user) {
            $this->addFlash('danger', 'Ce lien de réinitialisation n\'est pas valide.');
            return $this->redirectToRoute('app_reset_password_request');
        }
        
        // Vérifier si le token n'a pas expiré
        if ($user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('danger', 'Ce lien de réinitialisation a expiré.');
            return $this->redirectToRoute('app_reset_password_request');
        }
        
        // Traitement du formulaire de saisie de code
        if ($request->isMethod('POST')) {
            $submittedCode = $request->request->get('reset_code');
            
            // Vérifier que le code soumis correspond au code enregistré
            if ($submittedCode === $user->getResetCode()) {
                // Code valide, stocke le token en session pour la page de changement de mot de passe
                $request->getSession()->set('reset_token', $token);
                // Marquer le code comme vérifié
                $request->getSession()->set('code_verified', true);
                
                $this->addFlash('success', 'Code vérifié avec succès. Vous pouvez maintenant réinitialiser votre mot de passe.');
                
                return $this->redirectToRoute('app_reset_password_new');
            } else {
                $this->addFlash('danger', 'Le code saisi est incorrect. Veuillez réessayer.');
            }
        }
        
        return $this->render('user/verify_reset_code.html.twig', [
            'token' => $token
        ]);
    }
    
    /**
     * Page finale de changement du mot de passe après vérification du code
     */
    #[Route('/reset', name: 'app_reset_password_new')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Récupérer le token stocké en session
        $token = $request->getSession()->get('reset_token');
        if (null === $token) {
            $this->addFlash('danger', 'Vous devez d\'abord vérifier votre code de réinitialisation.');
            return $this->redirectToRoute('app_reset_password_request');
        }
        
        $user = $this->entityManager->getRepository(Utilisateur::class)->findOneBy([
            'resetToken' => $token
        ]);
        
        if (null === $user) {
            $this->addFlash('danger', 'Ce lien de réinitialisation n\'est pas valide.');
            return $this->redirectToRoute('app_reset_password_request');
        }
        
        // Vérifier si le token n'a pas expiré
        if ($user->getResetTokenExpiresAt() < new \DateTime()) {
            $this->addFlash('danger', 'Ce lien de réinitialisation a expiré.');
            return $this->redirectToRoute('app_reset_password_request');
        }
        
        // Vérifier si le code a été validé
        $codeVerified = $request->getSession()->get('code_verified');
        if (!$codeVerified) {
            $this->addFlash('danger', 'Vous devez d\'abord vérifier le code reçu par email.');
            return $this->redirectToRoute('app_verify_reset_code', ['token' => $token]);
        }
        
        // Créer le formulaire de réinitialisation
        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère le mot de passe en clair
            $plainPassword = $form->get('plainPassword')->getData();
            
            // Encode le nouveau mot de passe
            $encodedPassword = $passwordHasher->hashPassword($user, $plainPassword);
            $user->setMot_de_passe($encodedPassword);
            
            // Nettoie le token et le code de réinitialisation
            $user->setResetToken(null);
            $user->setResetTokenExpiresAt(null);
            $user->setResetCode(null);
            
            $this->entityManager->flush();
            $request->getSession()->remove('reset_token');
            $request->getSession()->remove('code_verified');
            
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès.');
            
            return $this->redirectToRoute('app_login');
        }
        
        return $this->render('user/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }
}
