<?php

namespace App\Controller;

use App\Entity\Utilisateur;
use App\Entity\Role;
use App\Repository\UtilisateurRepository;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        // Only allow access to users with ROLE_ADMIN
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        return $this->render('back/dashboard.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
    
    #[Route('/users', name: 'app_admin_users')]
    public function users(UtilisateurRepository $utilisateurRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $utilisateurs = $utilisateurRepository->findAll();
        
        return $this->render('back/users/index.html.twig', [
            'utilisateurs' => $utilisateurs,
        ]);
    }
    
    #[Route('/users/{id}/ban', name: 'app_admin_user_ban', methods: ['POST'])]
    public function banUser(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('ban'.$utilisateur->getId(), $request->request->get('_token'))) {
            // Toggle the user's ban status
            $utilisateur->setIsActive(!$utilisateur->getIsActive());
            $entityManager->flush();
            
            $status = $utilisateur->getIsActive() ? 'activé' : 'désactivé';
            $this->addFlash('success', "L'utilisateur a été $status avec succès.");
        } else {
            $this->addFlash('danger', "Action non autorisée: token CSRF invalide.");
        }
        
        return $this->redirectToRoute('app_admin_users');
    }
    
    #[Route('/users/{id}/change-role', name: 'app_admin_user_change_role', methods: ['POST'])]
    public function changeRole(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, RoleRepository $roleRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('role'.$utilisateur->getId(), $request->request->get('_token'))) {
            $roleId = $request->request->get('role');
            $role = $roleRepository->find($roleId);
            
            if ($role) {
                $oldRole = $utilisateur->getRole() ? $utilisateur->getRole()->getNom() : 'Aucun';
                $utilisateur->setRole($role);
                $entityManager->flush();
                
                $this->addFlash('success', "Le rôle de l'utilisateur a été modifié de '$oldRole' à '{$role->getNom()}' avec succès.");
            } else {
                $this->addFlash('danger', "Le rôle sélectionné n'existe pas ou est invalide.");
            }
        } else {
            $this->addFlash('danger', "Action non autorisée: token CSRF invalide.");
        }
        
        return $this->redirectToRoute('app_admin_users');
    }
    
    #[Route('/users/{id}/delete', name: 'app_admin_user_delete', methods: ['POST'])]
    public function deleteUser(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        // Vérifier le token CSRF
        if ($this->isCsrfTokenValid('delete'.$utilisateur->getId(), $request->request->get('_token'))) {
            $userName = $utilisateur->getPrenom() . ' ' . $utilisateur->getNom();
            
            try {
                $entityManager->remove($utilisateur);
                $entityManager->flush();
                $this->addFlash('success', "L'utilisateur '$userName' a été supprimé avec succès.");
            } catch (\Exception $e) {
                $this->addFlash('danger', "Impossible de supprimer l'utilisateur. Il pourrait être référencé par d'autres entités.");
            }
        } else {
            $this->addFlash('danger', "Action non autorisée: token CSRF invalide.");
        }
        
        return $this->redirectToRoute('app_admin_users');
    }
    
    #[Route('/users/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function editUser(Request $request, Utilisateur $utilisateur, EntityManagerInterface $entityManager, RoleRepository $roleRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        
        $roles = $roleRepository->findAll();
        
        if ($request->isMethod('POST')) {
            // Récupérer les données du formulaire
            $nom = $request->request->get('nom');
            $prenom = $request->request->get('prenom');
            $email = $request->request->get('email');
            $telephone = $request->request->get('telephone');
            $roleId = $request->request->get('role');
            $isActive = $request->request->get('isActive') ? true : false;
            
            // Mettre à jour l'utilisateur
            $utilisateur->setNom($nom);
            $utilisateur->setPrenom($prenom);
            $utilisateur->setEmail($email);
            $utilisateur->setNumeroTel($telephone);
            
            // Mettre à jour le rôle si sélectionné
            if ($roleId) {
                $role = $roleRepository->find($roleId);
                if ($role) {
                    $utilisateur->setRole($role);
                }
            }
            
            // Mettre à jour le statut
            $utilisateur->setIsActive($isActive);
            
            // Sauvegarder les modifications
            $entityManager->flush();
            
            // Message de succès
            $this->addFlash('success', "L'utilisateur a été modifié avec succès.");
            
            // Rediriger vers la liste des utilisateurs
            return $this->redirectToRoute('app_admin_users');
        }
        
        // Afficher le formulaire d'édition
        return $this->render('back/users/edit.html.twig', [
            'utilisateur' => $utilisateur,
            'roles' => $roles
        ]);
    }
}
