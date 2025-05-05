<?php

namespace App\Service;

use App\Entity\Reclamation;
use App\Entity\User;
use App\Service\PurgoMalumService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ReclamationService
{
    private $entityManager;
    private $validator;
    private $purgoMalumService;
    private $httpClient;
    private $logger;
    private $params;
    
    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        PurgoMalumService $purgoMalumService,
        HttpClientInterface $httpClient,
        ParameterBagInterface $params,
        LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->purgoMalumService = $purgoMalumService;
        $this->httpClient = $httpClient;
        $this->params = $params;
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
        // Vérifier les mots inappropriés dans la description
        if ($this->containsProfanity($reclamation->getDescription())) {
            $profanityList = $this->getProfanityList($reclamation->getDescription());
            return [
                'description' => 'La description contient des mots inappropriés : ' . implode(', ', $profanityList)
            ];
        }

        // Valider la réclamation
        $errors = $this->validateReclamation($reclamation);
        if (!empty($errors)) {
            return $errors;
        }

        // Sauvegarder la réclamation
        try {
            $this->entityManager->persist($reclamation);
            $this->entityManager->flush();

            // Envoyer un SMS si un numéro de téléphone est disponible
            if ($reclamation->getUtilisateur() && $reclamation->getUtilisateur()->getNumeroTel()) {
                $this->sendSms($reclamation, $reclamation->getUtilisateur()->getNumeroTel());
            }

            return null;
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Erreur lors de la sauvegarde de la réclamation : ' . $e->getMessage());
            }
            return ['global' => 'Une erreur est survenue lors de la sauvegarde de la réclamation.'];
        }
    }

    /**
     * Envoie un SMS de confirmation pour une réclamation
     * 
     * @param Reclamation $reclamation La réclamation concernée
     * @param string $phoneNumber Le numéro de téléphone du destinataire
     * @return bool True si l'envoi a réussi, false sinon
     */
    public function sendSms(Reclamation $reclamation, string $phoneNumber): bool
    {
        try {
            $formattedNumber = $this->formatPhoneNumber($phoneNumber);
            
            $response = $this->httpClient->request('POST', 'https://api.twilio.com/2010-04-01/Accounts/' . $this->params->get('twilio_sid') . '/Messages.json', [
                'auth_basic' => [$this->params->get('twilio_sid'), $this->params->get('twilio_token')],
                'body' => [
                    'To' => $formattedNumber,
                    'From' => $this->params->get('twilio_from'),
                    'MessagingServiceSid' => $this->params->get('twilio_messaging_service_sid'),
                    'Body' => sprintf(
                        'Votre réclamation #%d a bien été enregistrée. Nous la traiterons dans les plus brefs délais.',
                        $reclamation->getId()
                    )
                ]
            ]);

            return $response->getStatusCode() === 201;
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
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
    public function formatPhoneNumber(string $phoneNumber): string
    {
        // Supprimer tous les caractères non numériques
        $number = preg_replace('/[^0-9]/', '', $phoneNumber);
        
        // Si le numéro commence par 0, le remplacer par +33 (France)
        if (strlen($number) === 10 && substr($number, 0, 1) === '0') {
            return '+33' . substr($number, 1);
        }
        
        // Si le numéro commence par 216 (Tunisie), ajouter le +
        if (strlen($number) === 11 && substr($number, 0, 3) === '216') {
            return '+' . $number;
        }
        
        // Si le numéro ne commence pas par +, l'ajouter
        if (substr($number, 0, 1) !== '+') {
            return '+' . $number;
        }
        
        return $number;
    }
}
