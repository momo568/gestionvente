<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Promotion;
use App\Entity\User;
use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/super-admin')]
class SuperAdminController extends AbstractController
{
    // 🏠 Tableau de bord principal
    #[Route('/', name: 'super_admin_dashboard')]
    public function dashboard(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $totalCategories = $em->getRepository(Category::class)->count([]);
        $totalPromotions = $em->getRepository(Promotion::class)->count([]);
        $totalCommandes = $em->getRepository(Commande::class)->count([]);
        $totalUsers = $em->getRepository(User::class)->count([]);

        return $this->render('super_admin/dashboard.html.twig', [
            'totalCategories' => $totalCategories,
            'totalPromotions' => $totalPromotions,
            'totalCommandes' => $totalCommandes,
            'totalUsers' => $totalUsers,
        ]);
    }

    // 🗂️ Gestion des catégories
    #[Route('/categories', name: 'super_admin_categories')]
    public function categories(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('super_admin/categories.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/categories/add', name: 'super_admin_category_add')]
    public function addCategory(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom'));

            if (!$nom) {
                $this->addFlash('error', '⚠️ Le nom de la catégorie est obligatoire.');
                return $this->redirectToRoute('super_admin_categories');
            }

            $category = new Category();
            $category->setNom($nom);

            $em->persist($category);
            $em->flush();

            $this->addFlash('success', '✅ Catégorie ajoutée avec succès.');
            return $this->redirectToRoute('super_admin_categories');
        }

        return $this->render('super_admin/category_form.html.twig');
    }

    #[Route('/categories/delete/{id}', name: 'super_admin_category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $em->remove($category);
        $em->flush();

        $this->addFlash('success', '🗑️ Catégorie supprimée.');
        return $this->redirectToRoute('super_admin_categories');
    }

    // 💸 Gestion des promotions
    #[Route('/promotions', name: 'super_admin_promotions')]
    public function promotions(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $promotions = $em->getRepository(Promotion::class)->findAll();

        // ✅ On rend le bon fichier (pas de sous-dossier "index")
        return $this->render('super_admin/promotions.html.twig', [
            'promotions' => $promotions,
        ]);
    }

    #[Route('/promotions/add', name: 'super_admin_promo_add')]
    public function addPromotion(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        if ($request->isMethod('POST')) {
            $nom = trim($request->request->get('nom'));
            $code = strtoupper(trim($request->request->get('code')));
            $pourcentage = (float)$request->request->get('pourcentage');
            $dateDebutStr = $request->request->get('date_debut');
            $dateFinStr = $request->request->get('date_fin');
            $categoryId = $request->request->get('category_id');

            if (!$nom || !$code || $pourcentage <= 0) {
                $this->addFlash('error', '⚠️ Tous les champs obligatoires doivent être remplis.');
                return $this->redirectToRoute('super_admin_promotions');
            }

            $promo = new Promotion();
            $promo->setNom($nom);
            $promo->setCode($code);
            $promo->setPourcentage($pourcentage);

            // Sécurise les dates (évite le bug du 0000-00-00)
            if ($dateDebutStr) $promo->setDateDebut(new \DateTime($dateDebutStr));
            if ($dateFinStr) $promo->setDateFin(new \DateTime($dateFinStr));

            if ($categoryId) {
                $category = $em->getRepository(Category::class)->find($categoryId);
                $promo->setCategory($category);
            }

            $em->persist($promo);
            $em->flush();

            $this->addFlash('success', '🎁 Promotion ajoutée avec succès.');
            return $this->redirectToRoute('super_admin_promotions');
        }

        $categories = $em->getRepository(Category::class)->findAll();

        return $this->render('super_admin/promotion_form.html.twig', [
            'categories' => $categories,
        ]);
    }

    #[Route('/promotions/delete/{id}', name: 'super_admin_promo_delete')]
    public function deletePromotion(Promotion $promotion, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $em->remove($promotion);
        $em->flush();

        $this->addFlash('success', '🗑️ Promotion supprimée.');
        return $this->redirectToRoute('super_admin_promotions');
    }

    // 💰 Supervision des paiements
    #[Route('/paiements', name: 'super_admin_paiements')]
    public function paiements(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $commandes = $em->getRepository(Commande::class)->findAll();

        return $this->render('super_admin/paiements.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    // 🔑 Gestion des utilisateurs
    #[Route('/users', name: 'super_admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('super_admin/users.html.twig', [
            'users' => $users,
        ]);
    }
}
