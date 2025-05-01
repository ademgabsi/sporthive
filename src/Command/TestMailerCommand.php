<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class TestMailerCommand extends Command
{
    protected static $defaultName = 'app:test-mailer';
    protected static $defaultDescription = 'Test si la configuration du mailer fonctionne correctement';

    private $mailer;
    private $params;

    public function __construct(MailerInterface $mailer, ParameterBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->setHelp('Cette commande vous permet de tester si votre configuration MAILER_DSN fonctionne correctement.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('Test de la configuration du mailer');

        // Récupérer la configuration MAILER_DSN
        $dsn = $_ENV['MAILER_DSN'] ?? 'Non configuré';
        
        // Masquer les informations sensibles pour l'affichage
        $maskedDsn = preg_replace('/:(.*?)@/', ':***@', $dsn);
        
        $io->section('Configuration actuelle');
        $io->writeln("MAILER_DSN: <info>{$maskedDsn}</info>");
        
        $recipientEmail = 'izzatamri@gmail.com'; // Utilisez votre propre email pour le test
        
        $io->section('Envoi d\'un email de test');
        $io->writeln("Destinataire: <info>{$recipientEmail}</info>");
        
        try {
            $email = (new Email())
                ->from('support@sporthive.com')
                ->to($recipientEmail)
                ->subject('Sporthive - Test de configuration email')
                ->text('Ceci est un email de test pour vérifier la configuration du mailer dans Sporthive.')
                ->html('
                    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e9ecef; border-radius: 5px;">
                        <h1 style="color: #dc3545;">Sporthive - Test de configuration</h1>
                        <p>Ceci est un email de test pour vérifier que votre configuration MAILER_DSN fonctionne correctement.</p>
                        <p>Si vous recevez cet email, cela signifie que votre configuration est fonctionnelle.</p>
                        <p>Date et heure du test: <strong>'.date('Y-m-d H:i:s').'</strong></p>
                        <p>Configuration utilisée: <strong>'.$maskedDsn.'</strong></p>
                        <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 10px; border-radius: 5px; margin-top: 20px;">
                            <p>Cet email est généré automatiquement, merci de ne pas y répondre.</p>
                        </div>
                    </div>
                ');
            
            $io->writeln('Envoi en cours...');
            $startTime = microtime(true);
            
            $this->mailer->send($email);
            
            $endTime = microtime(true);
            $duration = round(($endTime - $startTime) * 1000);
            
            $io->success("Email envoyé avec succès en {$duration}ms!");
            $io->writeln("Vérifiez votre boîte de réception à <info>{$recipientEmail}</info>");
            
            // Afficher des suggestions de dépannage
            $io->section('Conseils en cas de problème');
            $io->writeln([
                "- Si vous n'avez pas reçu l'email, vérifiez votre dossier spam/indésirables",
                "- Pour Gmail: Assurez-vous que 'Accès moins sécurisé aux applications' est activé pour ce compte",
                "- Vérifiez que le mot de passe d'application Gmail est correct",
                "- Si vous utilisez Gmail, les espaces dans le mot de passe doivent être remplacés par des tirets (-)"
            ]);
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error("Erreur lors de l'envoi de l'email: " . $e->getMessage());
            
            // Aide au dépannage
            $io->section('Suggestions de dépannage');
            
            // Problèmes courants avec Gmail
            if (strpos($dsn, 'gmail.com') !== false) {
                $io->writeln([
                    '<comment>Problèmes courants avec Gmail:</comment>',
                    "- Assurez-vous d'utiliser un 'mot de passe d'application' et non votre mot de passe Gmail",
                    "- Les espaces dans le mot de passe doivent être remplacés par des tirets (-)",
                    "- Le format correct est: smtp://votre_email@gmail.com:votre-mot-de-passe@smtp.gmail.com:587",
                    "- Vérifiez si l'authentification à deux facteurs est activée sur votre compte Gmail"
                ]);
            }
            
            // Problèmes généraux
            $io->writeln([
                '<comment>Vérifications générales:</comment>',
                "- Vérifiez votre connexion Internet",
                "- Assurez-vous que le port 587 (ou 465 pour SSL) n'est pas bloqué par votre pare-feu",
                "- Essayez temporairement de désactiver votre antivirus pour tester",
                "- Pour déboguer plus en détail, ajoutez ?verify_peer=0 à la fin de votre DSN (à utiliser uniquement en développement)"
            ]);
            
            return Command::FAILURE;
        }
    }
}
