<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Service\ReclamationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[Route('/reclamation')]
class ReclamationController extends AbstractController
{
    #[Route('/', name: 'app_reclamation_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $reclamations = $entityManager
            ->getRepository(Reclamation::class)
            ->findAll();

        return $this->render('Gestion_Reclamation/Front/index.html.twig', [
            'reclamations' => $reclamations,
        ]);
    }

    #[Route('/new', name: 'app_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger, ReclamationService $reclamationService): Response
    {
        $reclamation = new Reclamation();
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documentFile */
            $documentFile = $form->get('Documents')->getData();

            // Si un fichier a été uploadé
            if ($documentFile) {
                $originalFilename = pathinfo($documentFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Sécuriser le nom du fichier
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$documentFile->guessExtension();

                // Déplacer le fichier dans le répertoire où les documents sont stockés
                try {
                    // Créer le répertoire s'il n'existe pas
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/fichier_reclamation';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    $documentFile->move(
                        $uploadDir,
                        $newFilename
                    );
                    
                    // Mettre à jour l'entité avec le nom du fichier
                    $reclamation->setDocuments($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe pendant l'upload
                    $this->addFlash('error', 'Une erreur s\'est produite lors de l\'upload du fichier');
                }
            }
            
            // Utiliser le service de réclamation pour sauvegarder et envoyer le SMS
            $errors = $reclamationService->saveReclamation($reclamation);
            
            if (empty($errors)) {
                $this->addFlash('success', 'Votre réclamation a été soumise avec succès. Un SMS de confirmation vous a été envoyé.');
                return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
            } else {
                // Afficher les erreurs
                foreach ($errors as $field => $message) {
                    $this->addFlash('error', $message);
                }
            }
        }

        return $this->render('Gestion_Reclamation/Front/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_reclamation}', name: 'app_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('Gestion_Reclamation/Front/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{ID_reclamation}/edit', name: 'app_reclamation_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(ReclamationType::class, $reclamation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $documentFile */
            $documentFile = $form->get('Documents')->getData();

            // Si un fichier a été uploadé
            if ($documentFile) {
                $originalFilename = pathinfo($documentFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Sécuriser le nom du fichier
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$documentFile->guessExtension();

                // Déplacer le fichier dans le répertoire où les documents sont stockés
                try {
                    // Créer le répertoire s'il n'existe pas
                    $uploadDir = $this->getParameter('kernel.project_dir') . '/public/fichier_reclamation';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    
                    // Supprimer l'ancien fichier s'il existe
                    $oldFilename = $reclamation->getDocuments();
                    if ($oldFilename && file_exists($uploadDir.'/'.$oldFilename)) {
                        unlink($uploadDir.'/'.$oldFilename);
                    }
                    
                    $documentFile->move(
                        $uploadDir,
                        $newFilename
                    );
                    
                    // Mettre à jour l'entité avec le nom du fichier
                    $reclamation->setDocuments($newFilename);
                } catch (FileException $e) {
                    // Gérer l'exception si quelque chose se passe pendant l'upload
                    $this->addFlash('error', 'Une erreur s\'est produite lors de l\'upload du fichier');
                }
            }
            
            $entityManager->flush();

            return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Reclamation/Front/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_reclamation}', name: 'app_reclamation_delete', methods: ['POST'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getID_reclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
}
