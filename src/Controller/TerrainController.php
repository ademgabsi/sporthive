<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Form\TerrainType;
use App\Repository\TerrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/terrain')]
final class TerrainController extends AbstractController
{
    #[Route('/search', name: 'app_terrain_search', methods: ['GET'])]
public function search(Request $request, TerrainRepository $terrainRepository): Response
{
    $query = $request->query->get('q', '');
    $isAdmin = $request->query->get('admin', 'false') === 'true';
    $sortBy = $request->query->get('sort_by', 'idTerrain');
    $direction = $request->query->get('direction', 'ASC');

    // Use the searchAndSort method to get sorted results
    $terrains = $terrainRepository->searchAndSort($query, $sortBy, $direction);

    $data = [];
    foreach ($terrains as $terrain) {
        $data[] = [
            'id' => $terrain->getIdTerrain(),
            'nom' => $terrain->getNom(),
            'typeSurface' => $terrain->getTypeSurface(),
            'localisation' => $terrain->getLocalisation(),
            'prix' => $terrain->getPrix(),
            'image' => $terrain->getImageTer()
        ];
    }

    return new JsonResponse($data);
}
    #[Route(name: 'app_terrain_index', methods: ['GET'])]
    public function index(TerrainRepository $terrainRepository): Response
    {
        return $this->render('terrain/front/index.html.twig', [
            'terrains' => $terrainRepository->findAll(),
        ]);
    }

    #[Route('/back', name: 'app_terrain_indexback', methods: ['GET'])]
public function backindex(TerrainRepository $terrainRepository, Request $request): Response
{
    $searchTerm = $request->query->get('q');
    $sortBy = $request->query->get('sort_by', 'idTerrain');
    $direction = $request->query->get('direction', 'ASC');

    $terrains = $terrainRepository->searchAndSort(
        $searchTerm,
        $sortBy,
        $direction
    );

    return $this->render('terrain/back/index.html.twig', [
        'terrains' => $terrains,
        'searchTerm' => $searchTerm,
        'current_sort' => $sortBy,
        'current_direction' => $direction
    ]);
}

    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageTer')->getData();
            if ($imageFile) {
                $imageFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('terrain_images_directory'),
                    $imageFilename
                );
                $terrain->setImageTer($imageFilename);
            }

            $entityManager->persist($terrain);
            $entityManager->flush();

            return $this->redirectToRoute('app_terrain_indexback', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('terrain/back/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form->createView(),
        ]);
    }
    #[Route('/front/new', name: 'app_terrain_newfront', methods: ['GET', 'POST'])]
    public function newFront(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageTer')->getData();
            if ($imageFile) {
                $imageFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('terrain_images_directory'),
                    $imageFilename
                );
                $terrain->setImageTer($imageFilename);
            }

            $entityManager->persist($terrain);
            $entityManager->flush();

            return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('terrain/front/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{idTerrain}', name: 'app_terrain_show', methods: ['GET'])]
    public function show(Terrain $terrain): Response
    {
        return $this->render('terrain/front/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }
    #[Route('back/{idTerrain}', name: 'app_terrainback_show', methods: ['GET'])]
    public function showback(Terrain $terrain): Response
    {
        return $this->render('terrain/back/show.html.twig', [
            'terrain' => $terrain,
        ]);
    }
    #[Route('/{idTerrain}/edit', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageTer')->getData();
            if ($imageFile) {
                $imageFilename = uniqid() . '.' . $imageFile->guessExtension();
                $imageFile->move(
                    $this->getParameter('terrain_images_directory'),
                    $imageFilename
                );
                $terrain->setImageTer($imageFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_terrain_indexback', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('terrain/back/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form->createView(),
        ]);
    }





    #[Route('/{idTerrain}', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $terrain->getIdTerrain(), $request->request->get('_token'))) {
            $entityManager->remove($terrain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_terrain_indexback', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/front/{idTerrain}/edit', name: 'app_terrain_edit_front', methods: ['GET', 'POST'])]
public function Frontedit(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
{
    $form = $this->createForm(TerrainType::class, $terrain);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $imageFile = $form->get('imageTer')->getData();
        if ($imageFile) {
            $imageFilename = uniqid() . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('terrain_images_directory'),
                $imageFilename
            );
            $terrain->setImageTer($imageFilename);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('terrain/front/edit.html.twig', [
        'terrain' => $terrain,
        'form' => $form->createView(),
    ]);
}

#[Route('/front/{idTerrain}', name: 'app_terrain_delete_front', methods: ['POST'])]
public function Frontdelete(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
{
    if ($this->isCsrfTokenValid('delete' . $terrain->getIdTerrain(), $request->request->get('_token'))) {
        $entityManager->remove($terrain);
        $entityManager->flush();
    }

    return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
}





















}
