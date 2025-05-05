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
use Dompdf\Dompdf;
use Dompdf\Options;
use Psr\Log\LoggerInterface;

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

    #[Route('/export/pdf', name: 'app_joueur_export_pdf', methods: ['GET'])]
    public function exportPdf(JoueurRepository $joueurRepository, LoggerInterface $logger): Response
    {
        try {
            $logger->info('Début de la génération du PDF des joueurs');
            
            $joueurs = $joueurRepository->findAll();
            $logger->info(sprintf('Nombre de joueurs récupérés : %d', count($joueurs)));
            
            $html = $this->renderView('joueur/pdf.html.twig', [
                'joueurs' => $joueurs
            ]);
            $logger->info('Template Twig rendu avec succès');
            
            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $logger->info('Options Dompdf configurées');
            
            $dompdf = new Dompdf($options);
            $logger->info('Instance Dompdf créée');
            
            $dompdf->loadHtml($html);
            $logger->info('HTML chargé dans Dompdf');
            
            $dompdf->setPaper('A4', 'portrait');
            $logger->info('Format de papier configuré');
            
            $dompdf->render();
            $logger->info('PDF généré avec succès');
            
            $output = $dompdf->output();
            $filename = 'joueurs_' . date('Y-m-d_H-i-s') . '.pdf';
            $logger->info(sprintf('Nom du fichier généré : %s', $filename));
            $logger->info(sprintf('Taille du PDF : %d octets', strlen($output)));
            
            $response = new Response($output);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');
            $response->headers->set('Cache-Control', 'private, max-age=0, must-revalidate');
            $response->headers->set('Pragma', 'public');
            $response->headers->set('Content-length', strlen($output));
            
            return $response;
        } catch (\Exception $e) {
            $logger->error('Erreur lors de la génération du PDF : ' . $e->getMessage());
            $this->addFlash('error', 'Une erreur est survenue lors de la génération du PDF.');
            return $this->redirectToRoute('app_joueur_index');
        }
    }

    #[Route('/search', name: 'app_joueur_search', methods: ['GET'])]
    public function search(Request $request, JoueurRepository $joueurRepository, LoggerInterface $logger): Response
    {
        try {
            $search = trim($request->query->get('search', ''));
            $logger->info('Recherche de joueurs avec le terme : ' . $search);

            if (empty($search)) {
                $joueurs = $joueurRepository->findAll();
                $logger->info('Terme de recherche vide, retour de tous les joueurs');
            } else {
                $qb = $joueurRepository->createQueryBuilder('j');
                $qb->where('j.nom LIKE :search')
                   ->setParameter('search', '%' . $search . '%')
                   ->orderBy('j.nom', 'ASC');

                $query = $qb->getQuery();
                $logger->info('Requête SQL : ' . $query->getSQL());
                $logger->info('Paramètres : ' . json_encode($query->getParameters()->toArray()));

                $joueurs = $query->getResult();
                $logger->info(sprintf('Nombre de joueurs trouvés : %d', count($joueurs)));
            }

            return $this->render('joueur/_joueurs_list.html.twig', [
                'joueurs' => $joueurs,
                'search' => $search
            ]);
        } catch (\Exception $e) {
            $logger->error('Erreur lors de la recherche des joueurs : ' . $e->getMessage());
            $logger->error('Stack trace : ' . $e->getTraceAsString());
            return $this->render('joueur/_joueurs_list.html.twig', [
                'joueurs' => [],
                'error' => 'Une erreur est survenue lors de la recherche : ' . $e->getMessage()
            ]);
        }
    }
}