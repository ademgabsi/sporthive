<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Form\RegistrationFormType;
use App\Form\ResetPasswordFormType;
use App\Form\ForgotPasswordFormType;
use App\Form\ProfileEditFormType;
use App\Repository\UtilisateurRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher,
        RoleRepository $roleRepository
    ): Response
    {
        $user = new Utilisateur();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            $user->setMot_de_passe($hashedPassword);
            
            // Assign a default role (user)
            $defaultRole = $roleRepository->findOneBy(['nom' => 'user']);
            if (!$defaultRole) {
                // Create the role if it doesn't exist
                $defaultRole = new Role();
                $defaultRole->setNom('user');
                $entityManager->persist($defaultRole);
            }
            $user->setRole($defaultRole);
            
            // Save the user
            $entityManager->persist($user);
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre compte a été créé avec succès!');
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        
        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('user/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'google_recaptcha_site_key' => $this->getParameter('google_recaptcha_site_key'),
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method can be empty - it will be intercepted by the logout key on your firewall
        throw new \Exception('This method should not be called directly');
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(
        Request $request,
        UtilisateurRepository $userRepository,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(ForgotPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                // Generate reset token (in a real app, you'd use a secure random token)
                $resetToken = bin2hex(random_bytes(32));
                $user->setResetToken($resetToken);
                $entityManager->flush();

                // In a real app, you would send the token via email
                // For now, we'll just simulate it with a flash message
                $this->addFlash('success', 'Si votre email existe dans notre système, vous recevrez un lien de réinitialisation.');
                
                // Redirect to reset password page with token (in a real app, this would be sent via email)
                return $this->redirectToRoute('app_reset_password', ['token' => $resetToken]);
            } else {
                // Don't reveal if the email exists for security reasons
                $this->addFlash('success', 'Si votre email existe dans notre système, vous recevrez un lien de réinitialisation.');
            }

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/forgot_password.html.twig', [
            'forgotPasswordForm' => $form->createView(),
        ]);
    }

    #[Route('/reset-password/{token}', name: 'app_reset_password')]
    public function resetPassword(
        Request $request,
        string $token,
        UtilisateurRepository $userRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('danger', 'Le lien de réinitialisation est invalide ou a expiré.');
            return $this->redirectToRoute('app_login');
        }

        $form = $this->createForm(ResetPasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Encode the new password
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()
            );
            
            $user->setMot_de_passe($hashedPassword);
            $user->setResetToken(null); // Clear the reset token
            
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre mot de passe a été réinitialisé avec succès!');
            
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/reset_password.html.twig', [
            'resetPasswordForm' => $form->createView(),
        ]);
    }

    #[Route('/profile', name: 'app_profile')]
    public function profile(): Response
    {
        // Make sure user is authenticated
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var Utilisateur $user */
        $user = $this->getUser();
        
        return $this->render('user/profile.html.twig', [
            'user' => $user,
        ]);
    }
    
    #[Route('/profile/edit', name: 'app_profile_edit')]
    public function editProfile(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Make sure user is authenticated
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        
        /** @var Utilisateur $user */
        $user = $this->getUser();
        
        $form = $this->createForm(ProfileEditFormType::class, $user);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle profile image upload
            $profileImageFile = $form->get('profileImage')->getData();
            
            if ($profileImageFile) {
                $originalFilename = pathinfo($profileImageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Sanitize filename - replace transliterator_transliterate with a simpler approach
                $safeFilename = $this->slugify($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$profileImageFile->guessExtension();
                
                // Move the file to the directory where profile images are stored
                try {
                    $uploadsDirectory = $this->getParameter('kernel.project_dir') . '/public/uploads/users';
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($uploadsDirectory)) {
                        mkdir($uploadsDirectory, 0777, true);
                    }
                    
                    $profileImageFile->move(
                        $uploadsDirectory,
                        $newFilename
                    );
                    
                    // Update user profile image
                    $user->setImage($newFilename);
                } catch (\Exception $e) {
                    $this->addFlash('danger', 'Une erreur est survenue lors du téléchargement de l\'image: ' . $e->getMessage());
                }
            }
            
            // Save changes
            $entityManager->flush();
            
            $this->addFlash('success', 'Votre profil a été mis à jour avec succès!');
            
            return $this->redirectToRoute('app_profile');
        }
        
        return $this->render('user/profile_edit.html.twig', [
            'profileForm' => $form->createView(),
            'user' => $user,
        ]);
    }
    
    /**
     * Simple function to sanitize a string for use as a filename
     */
    private function slugify(string $text): string
    {
        // Replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // Transliterate if possible (convert accented characters to ASCII)
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }
        
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // Trim
        $text = trim($text, '-');
        
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        
        // Lowercase
        $text = strtolower($text);
        
        // If empty, return 'n-a'
        if (empty($text)) {
            return 'n-a';
        }
        
        return $text;
    }
}
