<?php

namespace App\Controller;
use App\Entity\Joueur;
use App\Entity\Equipe;
use App\Repository\EquipeRepository;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/front')]
final class Equipe_frontController extends AbstractController
{
    #[Route('/', name: 'index_front', methods: ['GET'])]
    public function index_front(Request $request, EquipeRepository $equipeRepository, PaginatorInterface $paginator): Response
    {
        $query = $equipeRepository->createQueryBuilder('e')
                                   ->getQuery();
    
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            5
        );
    
        return $this->render('home/equipe_front.html.twig', [
            'pagination' => $pagination
        ]);
    }
    


    
#[Route('/joueur', name: 'index_front_joueur', methods: ['GET'])]
public function index_front_joueur(JoueurRepository $joueurRepository): Response
{
    return $this->render('home/joueur_front.html.twig', [
        'joueurs' => $joueurRepository->findAll(), 
    ]);
}

#[Route('/front/{id}', name: 'app_joueur_show_front', methods: ['GET'])]
    public function show(Joueur $joueur): Response
    {
        return $this->render('home/Profile_joueur_front.html.twig', [
            'joueur' => $joueur,
        ]);
    }

    #[Route('/frontEquipe/{id}', name: 'app_equipe_show_front', methods: ['GET'])]
    public function showEquipe(Equipe $equipe): Response
    {
        return $this->render('home/Profile_equipe.html.twig', [
            'equipe' => $equipe,
        ]);
    }


    #[Route('/chatbot/gemini', name: 'chatbot_gemini', methods: ['POST'])]
public function chatbotGemini(Request $request): JsonResponse
{
    $apiKey = "AIzaSyDQdsjlrFYKODaku3FSZ0fLC-pIckLY0lw";
    $userMessage = trim($request->request->get('message'));

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=" . $apiKey;

    $data = [
        "contents" => [
            [
                "parts" => [
                    ["text" => $userMessage]
                ]
            ]
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    curl_close($ch);

    $decoded = json_decode($response, true);

    $botResponse = "Je ne comprends pas.";
    if (isset($decoded["candidates"][0]["content"]["parts"][0]["text"])) {
        $botResponse = $decoded["candidates"][0]["content"]["parts"][0]["text"];
    }

    return new JsonResponse(["bot" => $botResponse]);
}

}