<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ChatbotController extends AbstractController
{
    #[Route('/api/chatbot/ask', name: 'chatbot_ask', methods: ['POST'])]
    public function ask(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $userMessage = $data['message'] ?? '';

        // Liste des réponses prédéfinies
        $responses = [
            'equipe' => 'Vous pouvez gérer vos équipes dans la section Équipes. Vous pouvez ajouter, modifier ou supprimer des équipes.',
            'joueur' => 'La section Joueurs vous permet de gérer les membres de vos équipes. Vous pouvez ajouter de nouveaux joueurs ou modifier leurs informations.',
            'match' => 'Vous pouvez consulter et gérer les matchs dans la section Match. Vous pouvez voir les résultats et les statistiques.',
            'assurance' => 'Nous proposons différentes formules d\'assurance pour vos équipes et joueurs. Consultez la section Assurance pour plus d\'informations.',
            'reclamation' => 'Pour toute réclamation, utilisez notre formulaire dans la section Réclamation. Nous vous répondrons dans les plus brefs délais.',
            'sponsor' => 'Vous pouvez gérer vos sponsors dans la section Sponsor. Ajoutez de nouveaux sponsors ou modifiez les existants.',
            'default' => 'Je suis l\'assistant SPORTHIVE. Comment puis-je vous aider ? Vous pouvez me poser des questions sur les équipes, les joueurs, les matchs, les assurances, les réclamations ou les sponsors.'
        ];

        // Recherche de mots-clés dans le message
        $message = strtolower($userMessage);
        $response = $responses['default'];

        foreach ($responses as $keyword => $answer) {
            if ($keyword !== 'default' && strpos($message, $keyword) !== false) {
                $response = $answer;
                break;
            }
        }

        return new JsonResponse(['bot' => $response]);
    }
}
