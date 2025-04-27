<?php

namespace App\Controller;

use App\Repository\EquipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;

class HomeController extends AbstractController
{
    public function __construct(
        private EquipeRepository $equipeRepository
    ) {
    }

    #[Route('/', name: 'app_home')]
public function index(Request $request, PaginatorInterface $paginator): Response
{
    $query = $this->equipeRepository->createQueryBuilder('e')
                                     ->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        6  // Nombre de cartes par page
    );

    return $this->render('home/index.html.twig', [
        'pagination' => $pagination
    ]);
}
}
