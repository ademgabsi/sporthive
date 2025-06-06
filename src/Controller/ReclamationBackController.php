<?php

namespace App\Controller;

use App\Entity\Reclamation;
use App\Form\ReclamationType;
use App\Repository\ReclamationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/admin/reclamation')]
final class ReclamationBackController extends AbstractController
{
    #[Route('/', name: 'app_admin_reclamation_index', methods: ['GET'])]
    public function index(ReclamationRepository $reclamationRepository): Response
    {
        return $this->render('Gestion_Reclamation/Back/index.html.twig', [
            'reclamations' => $reclamationRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_reclamation_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
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
            
            $entityManager->persist($reclamation);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Reclamation/Back/new.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_reclamation}', name: 'app_admin_reclamation_show', methods: ['GET'])]
    public function show(Reclamation $reclamation): Response
    {
        return $this->render('Gestion_Reclamation/Back/show.html.twig', [
            'reclamation' => $reclamation,
        ]);
    }

    #[Route('/{ID_reclamation}/edit', name: 'app_admin_reclamation_edit', methods: ['GET', 'POST'])]
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

            return $this->redirectToRoute('app_admin_reclamation_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('Gestion_Reclamation/Back/edit.html.twig', [
            'reclamation' => $reclamation,
            'form' => $form,
        ]);
    }

    #[Route('/{ID_reclamation}/delete', name: 'app_admin_reclamation_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Reclamation $reclamation, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reclamation->getID_reclamation(), $request->request->get('_token'))) {
            $entityManager->remove($reclamation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_reclamation_index', [], Response::HTTP_SEE_OTHER);
    }
    
    #[Route('/export/pdf', name: 'app_admin_reclamation_export_pdf', methods: ['GET'])]
    public function exportPdf(ReclamationRepository $reclamationRepository): Response
    {
        // Récupérer toutes les réclamations
        $reclamations = $reclamationRepository->findAll();
        
        // Configurer Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        // Générer le HTML pour le PDF
        $html = $this->renderView('Gestion_Reclamation/Back/pdf_template.html.twig', [
            'reclamations' => $reclamations,
            'date' => new \DateTime(),
        ]);
        
        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);
        
        // Configurer le format et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');
        
        // Rendre le PDF
        $dompdf->render();
        
        // Générer un nom de fichier
        $filename = 'reclamations_' . date('Y-m-d_H-i-s') . '.pdf';
        
        // Renvoyer le PDF comme réponse
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
    
    #[Route('/{ID_reclamation}/export/pdf', name: 'app_admin_reclamation_export_single_pdf', methods: ['GET'])]
    public function exportSinglePdf(Reclamation $reclamation): Response
    {
        // Configurer Dompdf
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        // Générer le HTML pour le PDF
        $html = $this->renderView('Gestion_Reclamation/Back/pdf_single_template.html.twig', [
            'reclamation' => $reclamation,
            'date' => new \DateTime(),
        ]);
        
        // Charger le HTML dans Dompdf
        $dompdf->loadHtml($html);
        
        // Configurer le format et l'orientation du papier
        $dompdf->setPaper('A4', 'portrait');
        
        // Rendre le PDF
        $dompdf->render();
        
        // Générer un nom de fichier
        $filename = 'reclamation_' . $reclamation->getID_reclamation() . '_' . date('Y-m-d') . '.pdf';
        
        // Renvoyer le PDF comme réponse
        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ]
        );
    }
}