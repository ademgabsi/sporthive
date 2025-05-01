<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

#[Route('/debug')]
class CodeViewerController extends AbstractController
{
    #[Route('/view-reset-code/{email}', name: 'app_debug_view_reset_code')]
    public function viewResetCode(string $email, EntityManagerInterface $entityManager): Response
    {
        // Cette page n'est que pour le développement et les tests !
        // NE PAS UTILISER EN PRODUCTION !
        
        $user = $entityManager->getRepository(Utilisateur::class)->findOneBy([
            'email' => $email,
        ]);
        
        if (!$user) {
            return $this->render('debug/view_reset_code.html.twig', [
                'email' => $email,
                'resetCode' => 'Utilisateur non trouvé',
                'resetToken' => 'N/A',
                'expiresAt' => 'N/A',
            ]);
        }
        
        return $this->render('debug/view_reset_code.html.twig', [
            'email' => $email,
            'resetCode' => $user->getResetCode(),
            'resetToken' => $user->getResetToken(),
            'expiresAt' => $user->getResetTokenExpiresAt() ? $user->getResetTokenExpiresAt()->format('Y-m-d H:i:s') : 'N/A',
        ]);
    }
    
    #[Route('/test-mailer', name: 'app_debug_test_mailer')]
    public function testMailer(MailerInterface $mailer): Response
    {
        $diagnosticInfo = [];
        $dsn = $_ENV['MAILER_DSN'] ?? 'Non configuré';
        $diagnosticInfo['dsn'] = preg_replace('/:[^:@]*@/', ':***@', $dsn);
        
        $testEmail = 'izzat@gmail.com'; // Remplacez par votre email
        $diagnosticInfo['test_email'] = $testEmail;
        
        $success = false;
        $errorDetails = null;
        
        try {
            $email = (new TemplatedEmail())
                ->from(new Address('support@sporthive.com', 'Sporthive Support'))
                ->to($testEmail)
                ->subject('Sporthive - Test de diagnostic email')
                ->htmlTemplate('emails/reset_password_code.html.twig')
                ->context([
                    'verifyUrl' => 'http://localhost:8000/test',
                    'tokenLifetime' => '1 heure',
                    'user' => null,
                    'resetCode' => '123456' // Code de test
                ]);
            
            $mailer->send($email);
            $success = true;
        } catch (TransportExceptionInterface $e) {
            $errorDetails = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'debug' => method_exists($e, 'getDebug') ? $e->getDebug() : null,
                'trace' => $e->getTraceAsString()
            ];
        } catch (\Exception $e) {
            $errorDetails = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ];
        }
        
        // Aussi essayer le SMTP Hostinger avec SwiftMailer directement
        $altSuccess = false;
        $altError = null;
        
        try {
            // Configuration manuelle de SMTP pour Hostinger
            $transport = new \Swift_SmtpTransport('smtp.hostinger.com', 465, 'ssl');
            $transport->setUsername('hala.omran@jameiconseil.org');
            $transport->setPassword('Oussama1981@');
            
            // Créer le mailer
            $swiftMailer = new \Swift_Mailer($transport);
            
            // Créer le message
            $message = (new \Swift_Message('Sporthive - Test direct SMTP'))
                ->setFrom(['support@sporthive.com' => 'Sporthive Support'])
                ->setTo([$testEmail])
                ->setBody('Ceci est un test direct SMTP avec SwiftMailer', 'text/plain');
            
            // Envoyer
            $altSuccess = $swiftMailer->send($message) > 0;
        } catch (\Exception $e) {
            $altError = [
                'message' => $e->getMessage(),
                'code' => $e->getCode(),
                'trace' => $e->getTraceAsString()
            ];
        }
        
        return $this->render('debug/test_mailer.html.twig', [
            'diagnosticInfo' => $diagnosticInfo,
            'success' => $success,
            'errorDetails' => $errorDetails,
            'altSuccess' => $altSuccess,
            'altError' => $altError
        ]);
    }
}
