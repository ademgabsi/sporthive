<?php

namespace App\Controller;

use App\Entity\Joueur;
use App\Form\JoueurType;
use App\Repository\JoueurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[Route('/joueur')]
final class JoueurController extends AbstractController
{
    #[Route(name: 'app_joueur_index', methods: ['GET'])]
    public function index(JoueurRepository $joueurRepository): Response
    {
        return $this->render('joueur/index.html.twig', [
            'joueurs' => $joueurRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_joueur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager) {
        $joueur = new Joueur();
        $form = $this->createForm(JoueurType::class, $joueur);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            
            if ($photoFile) {
                $newFilename = md5(uniqid()).'.'.$photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );
                $joueur->setPhoto($newFilename);
            }
    
            $entityManager->persist($joueur);
            $entityManager->flush();
    
            return $this->redirectToRoute('app_joueur_index');
        }
    
        return $this->render('joueur/new.html.twig', [
            'form' => $form->createView(),
            'joueur'=>$joueur
        ]);
    }
    #[Route('/{id}', name: 'app_joueur_show', methods: ['GET'])]
    public function show(Joueur $joueur): Response
    {
        return $this->render('joueur/show.html.twig', [
            'joueur' => $joueur,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_joueur_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        Joueur $joueur, 
        EntityManagerInterface $entityManager,
        SluggerInterface $slugger
    ): Response {
        $oldPhoto = $joueur->getPhoto();
        $form = $this->createForm(JoueurType::class, $joueur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            
            if ($photoFile) {
                // Supprimer l'ancienne photo si elle existe
                if ($oldPhoto) {
                    $oldPhotoPath = $this->getParameter('photos_directory').'/'.$oldPhoto;
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
                
                $originalFilename = pathinfo($photoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$photoFile->guessExtension();
                
                try {
                    $photoFile->move(
                        $this->getParameter('photos_directory'),
                        $newFilename
                    );
                    $joueur->setPhoto($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement de la photo.');
                    return $this->redirectToRoute('app_joueur_edit', ['id' => $joueur->getId()]);
                }
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le joueur a été modifié avec succès');
            return $this->redirectToRoute('app_joueur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('joueur/edit.html.twig', [
            'joueur' => $joueur,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_joueur_delete', methods: ['POST'])]
    public function delete(
        Request $request, 
        Joueur $joueur, 
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete'.$joueur->getId(), $request->getPayload()->get('_token'))) {
            // Supprimer la photo associée
            $photo = $joueur->getPhoto();
            if ($photo) {
                $photoPath = $this->getParameter('photos_directory').'/'.$photo;
                if (file_exists($photoPath)) {
                    unlink($photoPath);
                }
            }
            
            $entityManager->remove($joueur);
            $entityManager->flush();
            
            $this->addFlash('success', 'Le joueur a été supprimé avec succès');
        }

        return $this->redirectToRoute('app_joueur_index', [], Response::HTTP_SEE_OTHER);
    }
}