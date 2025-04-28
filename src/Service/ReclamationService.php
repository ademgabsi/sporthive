<?php

namespace App\Service;

use App\Entity\Reclamation;
use App\Service\PurgoMalumService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ReclamationService
{
    private $entityManager;
    private $validator;
    private $purgoMalumService;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator,
        PurgoMalumService $purgoMalumService
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
        $this->purgoMalumService = $purgoMalumService;
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
            return null;
        }
        
        return $errors;
    }
}
