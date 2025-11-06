<?php

namespace App\Controller;

use App\Repository\ClientRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/dashboard')]
class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(
        ClientRepository $clientRepo,
        ProduitRepository $produitRepo,
        CommandeRepository $commandeRepo
    ): Response {
        // === 1️⃣ Statistiques globales ===
        $totalClients = $clientRepo->count([]);
        $totalProduits = $produitRepo->count([]);
        $totalCommandes = $commandeRepo->count([]);

        $revenuTotal = 0;
        foreach ($commandeRepo->findAll() as $commande) {
            $revenuTotal += $commande->getTotal();
        }

        // === 2️⃣ Graphique en courbe : ventes par mois ===
        $ventesParMois = [];
        foreach ($commandeRepo->findAll() as $commande) {
            $mois = $commande->getDateCommande()->format('Y-m');
            if (!isset($ventesParMois[$mois])) {
                $ventesParMois[$mois] = 0;
            }
            $ventesParMois[$mois] += $commande->getTotal();
        }

        ksort($ventesParMois);
        $labelsCourbe = array_keys($ventesParMois);
        $dataCourbe = array_values($ventesParMois);

        // === 3️⃣ Graphique camembert : ventes par produit ===
        $ventesParProduit = [];
        foreach ($commandeRepo->findAll() as $commande) {
            // Exemple basique : on simule une répartition aléatoire
            // Tu peux remplacer par une vraie relation commande → produit
            $produits = $produitRepo->findAll();
            foreach ($produits as $p) {
                if (!isset($ventesParProduit[$p->getNom()])) {
                    $ventesParProduit[$p->getNom()] = rand(1, 5) * 100;
                }
            }
        }

        $labelsCamembert = array_keys($ventesParProduit);
        $dataCamembert = array_values($ventesParProduit);

        // === 4️⃣ Graphique barres : stock restant ===
        $produits = $produitRepo->findAll();
        $labelsStock = [];
        $dataStock = [];

        foreach ($produits as $p) {
            $labelsStock[] = $p->getNom();
            $dataStock[] = $p->getStock();
        }

        return $this->render('dashboard/index.html.twig', [
            'totalClients' => $totalClients,
            'totalProduits' => $totalProduits,
            'totalCommandes' => $totalCommandes,
            'revenuTotal' => $revenuTotal,
            'labelsCourbe' => json_encode($labelsCourbe),
            'dataCourbe' => json_encode($dataCourbe),
            'labelsCamembert' => json_encode($labelsCamembert),
            'dataCamembert' => json_encode($dataCamembert),
            'labelsStock' => json_encode($labelsStock),
            'dataStock' => json_encode($dataStock),
        ]);
    }
}
