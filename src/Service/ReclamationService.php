<?php

namespace App\Service;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Service\PurgoMalumService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class ReclamationService
{
    private $entityManager;
    private $validator;
    private $purgoMalumService;
    private $httpClient;
    private $logger;
    
    // Identifiants Twilio directement dans le service
    private $twilioSid = 'AC430cb248a12bc16686ff7467d632578c';
    private $twilioToken = '0c332b1a35b10f12e8e3bb0d6d6eb2f0'; // Token d'authentification
    private $twilioFrom = '+19804094461';
    private $twilioMessagingServiceSid = 'MGa37ba5a8f1ba6ab48af8dec1667e56c3';

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        PurgoMalumService $purgoMalumService,
        HttpClientInterface $httpClient,
        LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->purgoMalumService = $purgoMalumService;
        $this->httpClient = $httpClient;
        $this->logger = $logger;
    }

    /**
     * Vérifie si la description d'une réclamation contient des mots inappropriés
     * 
     * @param string $description Le texte à vérifier
     * @return bool True si le texte contient des mots inappropriés, false sinon
     */
    public function containsProfanity(string $description): bool
    {
        return $this->purgoMalumService->containsProfanity($description);
    }

    /**
     * Récupère la liste des mots inappropriés dans une description
     * 
     * @param string $description Le texte à analyser
     * @return array La liste des mots inappropriés trouvés
     */
    public function getProfanityList(string $description): array
    {
        return $this->purgoMalumService->getProfanityList($description);
    }

    /**
     * Filtre les mots inappropriés dans une description
     * 
     * @param string $description Le texte à filtrer
     * @param string $replacement Le caractère de remplacement (par défaut *)
     * @return string Le texte filtré
     */
    public function filterProfanity(string $description, string $replacement = '*'): string
    {
        return $this->purgoMalumService->filterProfanity($description, $replacement);
    }

    /**
     * Valide une réclamation et retourne les éventuelles erreurs
     * 
     * @param Reclamation $reclamation La réclamation à valider
     * @return array Les erreurs de validation, tableau vide si aucune erreur
     */
    public function validateReclamation(Reclamation $reclamation): array
    {
        $errors = [];
        $violations = $this->validator->validate($reclamation);

        if (count($violations) > 0) {
            foreach ($violations as $violation) {
                $propertyPath = $violation->getPropertyPath();
                $errors[$propertyPath] = $violation->getMessage();
            }
        }

        return $errors;
    }

    /**
     * Sauvegarde une réclamation après avoir vérifié les mots inappropriés
     * 
     * @param Reclamation $reclamation La réclamation à sauvegarder
     * @return array|null Les erreurs de validation ou null si la sauvegarde a réussi
     */
    public function saveReclamation(Reclamation $reclamation): ?array
    {
        // Vérifier si la description contient des mots inappropriés
        $errors = [];
        
        if ($reclamation->getDescription() && $this->containsProfanity($reclamation->getDescription())) {
            // Récupérer la liste des mots inappropriés
            $profanityList = $this->getProfanityList($reclamation->getDescription());
            $badWords = !empty($profanityList) ? implode(', ', $profanityList) : 'mots inappropriés';
            
            // Ajouter un message d'erreur
            $errors['Description'] = "La description contient des mots inappropriés: {$badWords}";
            
            // Retourner les erreurs sans sauvegarder
            return $errors;
        }

        // Valider la réclamation
        $errors = $this->validateReclamation($reclamation);
        
        // S'il n'y a pas d'erreurs, on sauvegarde
        if (empty($errors)) {
            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();
            
            // Récupérer l'utilisateur associé à l'assurance pour envoyer le SMS
            $assurance = $reclamation->getAssurance();
            if ($assurance && $assurance->getUtilisateur()) {
                $utilisateur = $assurance->getUtilisateur();
                $phoneNumber = $utilisateur->getNumero_tel();
                
                if ($phoneNumber) {
                    // Envoyer un SMS de confirmation
                    $this->sendSms($reclamation, $phoneNumber);
                }
            }
            
            return null;
        }
        
        return $errors;
    }
    
    /**
     * Envoie un SMS de confirmation pour une réclamation
     * 
     * @param Reclamation $reclamation La réclamation concernée
     * @param string $phoneNumber Le numéro de téléphone du destinataire
     * @return bool True si l'envoi a réussi, false sinon
     */
    private function sendSms(Reclamation $reclamation, string $phoneNumber): bool
    {
        try {
            // Formater le numéro de téléphone pour l'envoi international
            $formattedPhoneNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Créer le message SMS
            $message = sprintf(
                "Sporthive: Votre réclamation #%s a été soumise avec succès. Nous l'examinerons dans les plus brefs délais.",
                $reclamation->getID_reclamation() ?: 'NEW'
            );
            
            // Log la tentative d'envoi
            if ($this->logger) {
                $this->logger->info('Tentative d\'envoi de SMS pour réclamation', [
                    'reclamation_id' => $reclamation->getID_reclamation(),
                    'phone' => $formattedPhoneNumber
                ]);
            }
            
            // Appel direct à l'API Twilio
            $response = $this->httpClient->request('POST', "https://api.twilio.com/2010-04-01/Accounts/{$this->twilioSid}/Messages.json", [
                'auth_basic' => [$this->twilioSid, $this->twilioToken],
                'body' => [
                    'To' => $formattedPhoneNumber,
                    'MessagingServiceSid' => $this->twilioMessagingServiceSid,
                    'Body' => $message
                ]
            ]);
            
            // Afficher les détails de la réponse pour le débogage
            if ($this->logger) {
                $this->logger->debug('Réponse Twilio brute', [
                    'status_code' => $response->getStatusCode(),
                    'content' => $response->getContent(false)
                ]);
            }
            
            $data = $response->toArray();
            
            // Log le succès
            if ($this->logger) {
                $this->logger->info('SMS envoyé avec succès', [
                    'reclamation_id' => $reclamation->getID_reclamation(),
                    'twilio_sid' => $data['sid'] ?? 'N/A',
                    'status' => $data['status'] ?? 'N/A'
                ]);
            }
            
            return true;
        } catch (\Exception $e) {
            // Log l'erreur
            if ($this->logger) {
                $this->logger->error('Erreur lors de l\'envoi du SMS', [
                    'reclamation_id' => $reclamation->getID_reclamation(),
                    'phone' => $phoneNumber,
                    'error' => $e->getMessage()
                ]);
            }
            
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
