<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class PurgoMalumService
{
    private $httpClient;
    private $customBadWords;
    private $parameterBag;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
        $this->customBadWords = $this->getCustomBadWords();
    }
    
    /**
     * Récupère la liste des mots personnalisés à filtrer
     * 
     * @return array Liste des mots personnalisés
     */
    private function getCustomBadWords(): array
    {
        // Par défaut, on utilise une liste vide
        $customWords = [];
        
        // Si le paramètre existe dans services.yaml, on l'utilise
        if ($this->parameterBag->has('app.custom_bad_words')) {
            $customWords = $this->parameterBag->get('app.custom_bad_words');
        }
        
        return $customWords;
    }

    /**
     * Vérifie si le texte contient des mots inappropriés
     * 
     * @param string $text Le texte à vérifier
     * @param array $additionalWords Mots supplémentaires à vérifier (en plus des mots par défaut et des mots personnalisés)
     * @return bool True si le texte contient des mots inappropriés, false sinon
     */
    public function containsProfanity(string $text, array $additionalWords = []): bool
    {
        try {
            // Fusionner les mots personnalisés avec les mots supplémentaires
            $addWords = array_merge($this->customBadWords, $additionalWords);
            $addParam = !empty($addWords) ? implode(',', $addWords) : null;
            
            $query = ['text' => $text];
            
            // Ajouter les mots personnalisés à la requête si nécessaire
            if ($addParam) {
                $query['add'] = $addParam;
            }
            
            $response = $this->httpClient->request('GET', 'https://www.purgomalum.com/service/containsprofanity', [
                'query' => $query
            ]);

            return $response->getContent() === 'true';
        } catch (\Exception $e) {
            // En cas d'erreur avec l'API, on log l'erreur et on retourne false pour ne pas bloquer l'utilisateur
            // Dans un environnement de production, vous pourriez vouloir logger cette erreur
            return false;
        }
    }

    /**
     * Filtre les mots inappropriés dans un texte
     * 
     * @param string $text Le texte à filtrer
     * @param string $replacement Le caractère de remplacement (par défaut *)
     * @param array $additionalWords Mots supplémentaires à filtrer (en plus des mots par défaut et des mots personnalisés)
     * @return string Le texte filtré
     */
    public function filterProfanity(string $text, string $replacement = '*', array $additionalWords = []): string
    {
        try {
            // Fusionner les mots personnalisés avec les mots supplémentaires
            $addWords = array_merge($this->customBadWords, $additionalWords);
            $addParam = !empty($addWords) ? implode(',', $addWords) : null;
            
            $query = [
                'text' => $text,
                'fill_char' => $replacement
            ];
            
            // Ajouter les mots personnalisés à la requête si nécessaire
            if ($addParam) {
                $query['add'] = $addParam;
            }
            
            $response = $this->httpClient->request('GET', 'https://www.purgomalum.com/service/json', [
                'query' => $query
            ]);

            $data = json_decode($response->getContent(), true);
            return $data['result'] ?? $text;
        } catch (\Exception $e) {
            // En cas d'erreur, on retourne le texte original
            return $text;
        }
    }

    /**
     * Récupère la liste des mots inappropriés trouvés dans un texte
     * 
     * @param string $text Le texte à analyser
     * @param array $additionalWords Mots supplémentaires à vérifier (en plus des mots par défaut et des mots personnalisés)
     * @return array La liste des mots inappropriés trouvés
     */
    public function getProfanityList(string $text, array $additionalWords = []): array
    {
        try {
            // Fusionner les mots personnalisés avec les mots supplémentaires
            $addWords = array_merge($this->customBadWords, $additionalWords);
            $addParam = !empty($addWords) ? implode(',', $addWords) : null;
            
            $query = [
                'text' => $text,
                'add' => 'profanity_list'
            ];
            
            // Ajouter les mots personnalisés à la requête si nécessaire
            if ($addParam) {
                $query['add'] = $addParam . ',profanity_list';
            }
            
            $response = $this->httpClient->request('GET', 'https://www.purgomalum.com/service/json', [
                'query' => $query
            ]);

            $data = json_decode($response->getContent(), true);
            return $data['profanity_list'] ?? [];
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Ajoute des mots personnalisés à la liste des mots filtrés
     * 
     * @param array $words Liste des mots à ajouter
     * @return array Liste complète des mots personnalisés après ajout
     */
    public function addCustomBadWords(array $words): array
    {
        $this->customBadWords = array_unique(array_merge($this->customBadWords, $words));
        return $this->customBadWords;
    }
    
    /**
     * Définit la liste complète des mots personnalisés
     * 
     * @param array $words Nouvelle liste des mots personnalisés
     * @return array Liste complète des mots personnalisés
     */
    public function setCustomBadWords(array $words): array
    {
        $this->customBadWords = $words;
        return $this->customBadWords;
    }
    
    /**
     * Récupère la liste actuelle des mots personnalisés
     * 
     * @return array Liste des mots personnalisés
     */
    public function getCurrentCustomBadWords(): array
    {
        return $this->customBadWords;
    }
}
