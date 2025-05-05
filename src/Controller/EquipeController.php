<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Equipe;
use App\Form\EquipeType;
use App\Repository\EquipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Psr\Log\LoggerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
use Dompdf\Options;

#[Route('/equipe')]
class EquipeController extends AbstractController
{
    private $equipeRepository;

    public function __construct(EquipeRepository $equipeRepository)
    {
        $this->equipeRepository = $equipeRepository;
    }

    #[Route('/', name: 'app_equipe_index', methods: ['GET'])]
public function index(Request $request, PaginatorInterface $paginator): Response
{
    $query = $this->equipeRepository->createQueryBuilder('e')
                                     ->getQuery();

    $pagination = $paginator->paginate(
        $query, /* Query, pas un tableau */
        $request->query->getInt('page', 1), /* page actuelle */
        5 /* Limite par page */
    );

    return $this->render('equipe/index.html.twig', [
        'pagination' => $pagination
    ]);}
    #[Route('/search', name: 'app_equipe_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $search = $request->query->get('search', '');
        $equipes = $search ? 
            $this->equipeRepository->searchByNomOrVille($search) : 
            $this->equipeRepository->findAll();

        return $this->render('equipe/_equipes_list.html.twig', [
            'equipes' => $equipes,
            'search' => $search
        ]);
    }
    #[Route('/new', name: 'app_equipe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $equipe = new Equipe();
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Gestion du logo
                $logoFile = $form->get('logo')->getData();
                if ($logoFile) {
                    $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $safeFilename = $slugger->slug($originalFilename);
                    $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();
    
                    try {
                        $logoFile->move($this->getParameter('logos_directory'), $newFilename);
                        $equipe->setLogo($newFilename);
                    } catch (FileException $e) {
                        throw new \Exception('Erreur lors du téléchargement du logo : ' . $e->getMessage());
                    }
                }
    
                // Persistance de l'équipe
                $entityManager->persist($equipe);
                $entityManager->flush();
    
                // Création du dossier qrcodes s'il n'existe pas
                $qrcodesDir = $this->getParameter('kernel.project_dir').'/public/qrcodes';
                if (!is_dir($qrcodesDir)) {
                    if (!mkdir($qrcodesDir, 0777, true)) {
                        throw new \Exception('Impossible de créer le dossier qrcodes');
                    }
                }
    
                // Génération du QR Code
                $qrContent = json_encode([
                    'id' => $equipe->getId(),
                    'nom' => $equipe->getNom(),
                    'ville' => $equipe->getVille(),
                    'stade' => $equipe->getStade()
                ]);
                $qrPath = $qrcodesDir.'/equipe-'.$equipe->getId().'.png';
    
                $result = Builder::create()
                    ->writer(new PngWriter())
                    ->data($qrContent)
                    ->size(300)
                    ->margin(10)
                    ->build();
                
                if (!$result->saveToFile($qrPath)) {
                    throw new \Exception('Erreur lors de la sauvegarde du QR Code');
                }
    
                $this->addFlash('success', 'L\'équipe a été créée avec succès et le QR Code généré.');
                return $this->redirectToRoute('app_equipe_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Erreur lors de la création de l\'équipe.');
                return $this->redirectToRoute('app_equipe_new');
            }
        }
    
        return $this->render('equipe/new.html.twig', [
            'equipe' => $equipe,
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/{id}', name: 'app_equipe_show', methods: ['GET'])]
    public function show(Equipe $equipe): Response
    {
        return $this->render('equipe/show.html.twig', [
            'equipe' => $equipe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_equipe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Equipe $equipe, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(EquipeType::class, $equipe);
        $form->handleRequest($request);
        $oldLogo = $equipe->getLogo();

        if ($form->isSubmitted() && $form->isValid()) {
            $logoFile = $form->get('logo')->getData();
            
            if ($logoFile) {
                if ($oldLogo) {
                    $oldLogoPath = $this->getParameter('logos_directory').'/'.$oldLogo;
                    if (file_exists($oldLogoPath)) {
                        unlink($oldLogoPath);
                    }
                }
                
                $originalFilename = pathinfo($logoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$logoFile->guessExtension();
                
                try {
                    $logoFile->move(
                        $this->getParameter('logos_directory'),
                        $newFilename
                    );
                    $equipe->setLogo($newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur est survenue lors du téléchargement du logo.');
                    return $this->redirectToRoute('app_equipe_edit', ['id' => $equipe->getId()]);
                }
            }

            try {
                $entityManager->flush();
                $this->addFlash('success', 'L\'équipe a été modifiée avec succès.');
                return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la modification de l\'équipe.');
                return $this->redirectToRoute('app_equipe_edit', ['id' => $equipe->getId()]);
            }
        }

        return $this->render('equipe/edit.html.twig', [
            'equipe' => $equipe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_equipe_delete', methods: ['POST'])]
    public function delete(Request $request, Equipe $equipe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$equipe->getId(), $request->getPayload()->getString('_token'))) {
            try {
                $logo = $equipe->getLogo();
                if ($logo) {
                    $logoPath = $this->getParameter('logos_directory').'/'.$logo;
                    if (file_exists($logoPath)) {
                        unlink($logoPath);
                    }
                }
                
                $entityManager->remove($equipe);
                $entityManager->flush();
                $this->addFlash('success', 'L\'équipe a été supprimée avec succès.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la suppression de l\'équipe.');
            }
        }

        return $this->redirectToRoute('app_equipe_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/index_front', name: 'index_front', methods: ['GET'])]
    public function index_front(): Response
    {
        return $this->render('home/equipe_front.html.twig', [
            'equipes' => $this->equipeRepository->findAll(), 
        ]);
    }
    
    #[Route('/{id}/joueurs', name: 'app_equipe_joueurs', methods: ['GET'])]
    public function showJoueurs(Equipe $equipe): Response
    {
        return $this->render('home/equipe_joueurs.html.twig', [
            'equipe' => $equipe,
            'joueurs' => $equipe->getJoueurs(),
        ]);
    }

    #[Route('/export/pdf', name: 'app_equipe_export_pdf', methods: ['GET'])]
    public function exportPdf(EquipeRepository $equipeRepository, LoggerInterface $logger): Response
    {
        try {
            $logger->info('Début de la génération du PDF');
            
            $equipes = $equipeRepository->findAll();
            $logger->info(sprintf('Nombre d\'équipes récupérées : %d', count($equipes)));
            
            $html = $this->renderView('equipe/pdf.html.twig', [
                'equipes' => $equipes
            ]);
            $logger->info('Template Twig rendu avec succès');
            
            // Créer une nouvelle instance de Dompdf
            $dompdf = new \Dompdf\Dompdf();
            
            // Configurer les options
            $options = new \Dompdf\Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);
            $options->set('isRemoteEnabled', true);
            $dompdf->setOptions($options);
            
            $logger->info('Options Dompdf configurées');
            
            $dompdf->loadHtml($html);
            $logger->info('HTML chargé dans Dompdf');
            
            $dompdf->setPaper('A4', 'portrait');
            $logger->info('Format de papier configuré');
            
            $dompdf->render();
            $logger->info('PDF généré avec succès');
            
            $output = $dompdf->output();
            $filename = 'equipes_' . date('Y-m-d_H-i-s') . '.pdf';
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
            return $this->redirectToRoute('app_equipe_index');
        }
    }


    #[Route('/equipe/check-latest', name: 'check_latest_equipe', methods: ['GET'])]
public function checkLatestEquipe(EquipeRepository $equipeRepository): JsonResponse
{
    $latestEquipe = $equipeRepository->findOneBy([], ['id' => 'DESC']);

    return new JsonResponse([
        'id' => $latestEquipe->getId(),
        'nom' => $latestEquipe->getNom(),
    ]);
}

}
