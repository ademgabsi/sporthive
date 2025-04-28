<?php

namespace App\Service;

use App\Entity\Reclamation;
use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\SmsMessage;
use Symfony\Component\Notifier\TexterInterface;
use Symfony\Component\Notifier\Notification\Notification;
use Symfony\Component\Notifier\Notification\SmsNotificationInterface;
use Symfony\Component\Notifier\Recipient\SmsRecipient;

class NotificationService
{
    private $texter;
    private $logger;

    public function __construct(TexterInterface $texter, LoggerInterface $logger = null)
    {
        $this->texter = $texter;
        $this->logger = $logger;
    }

    /**
     * Envoie une notification SMS pour confirmer la soumission d'une réclamation
     *
     * @param Reclamation $reclamation La réclamation soumise
     * @param string $phoneNumber Le numéro de téléphone du destinataire
     * @return bool True si l'envoi a réussi, false sinon
     */
    public function sendReclamationSubmissionSms(Reclamation $reclamation, string $phoneNumber): bool
    {
        try {
            // Formater le numéro de téléphone (ajouter +33 si nécessaire)
            $formattedPhoneNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Log le numéro de téléphone avant et après formatage
            if ($this->logger) {
                $this->logger->info('Tentative d\'envoi de SMS', [
                    'phone_original' => $phoneNumber,
                    'phone_formatted' => $formattedPhoneNumber,
                    'reclamation_id' => $reclamation->getID_reclamation() ?: 'NEW'
                ]);
            }
            
            // Créer le message SMS
            $message = new SmsMessage(
                $formattedPhoneNumber,
                sprintf(
                    "Sporthive: Votre réclamation #%s a été soumise avec succès. Nous l'examinerons dans les plus brefs délais.",
                    $reclamation->getID_reclamation() ?: 'NEW'
                )
            );
            
            // Envoyer le SMS
            $this->texter->send($message);
            
            if ($this->logger) {
                $this->logger->info('SMS envoyé avec succès', [
                    'phone' => $formattedPhoneNumber,
                    'reclamation_id' => $reclamation->getID_reclamation() ?: 'NEW'
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            // En cas d'erreur, on log l'erreur et on retourne false
            if ($this->logger) {
                $this->logger->error('Erreur lors de l\'envoi du SMS', [
                    'error' => $e->getMessage(),
                    'phone' => $phoneNumber,
                    'reclamation_id' => $reclamation->getID_reclamation() ?: 'NEW'
                ]);
            }
            return false;
        }
    }
    
    /**
     * Envoie une notification SMS pour informer de l'état d'une réclamation
     *
     * @param Reclamation $reclamation La réclamation mise à jour
     * @param string $phoneNumber Le numéro de téléphone du destinataire
     * @return bool True si l'envoi a réussi, false sinon
     */
    public function sendReclamationStatusSms(Reclamation $reclamation, string $phoneNumber): bool
    {
        try {
            // Formater le numéro de téléphone
            $formattedPhoneNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Créer le message SMS
            $message = new SmsMessage(
                $formattedPhoneNumber,
                sprintf(
                    "Sporthive: L'état de votre réclamation #%s a été mis à jour: %s. Consultez votre espace client pour plus de détails.",
                    $reclamation->getID_reclamation(),
                    $reclamation->getEtat()
                )
            );
            
            // Envoyer le SMS
            $this->texter->send($message);
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Formate un numéro de téléphone pour l'envoi de SMS
     * 
     * @param string $phoneNumber Le numéro de téléphone à formater
     * @return string Le numéro formaté
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Si le numéro est déjà au format international avec +, le conserver tel quel
        if (strpos($phoneNumber, '+') === 0) {
            // Supprimer les espaces, tirets, etc. mais conserver le +
            return '+' . preg_replace('/[^0-9]/', '', substr($phoneNumber, 1));
        }
        
        // Supprimer les espaces, tirets, etc.
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Détecter le pays en fonction du préfixe
        if (substr($phoneNumber, 0, 3) === '216') {
            // Numéro tunisien déjà avec l'indicatif mais sans +
            return '+' . $phoneNumber;
        } else if (substr($phoneNumber, 0, 1) === '0') {
            // Numéro français commençant par 0
            return '+33' . substr($phoneNumber, 1);
        }
        
        // Par défaut, ajouter simplement un + si nécessaire
        return '+' . $phoneNumber;
    }
}
