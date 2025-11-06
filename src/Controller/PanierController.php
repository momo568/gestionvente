<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/panier')]
class PanierController extends AbstractController
{
    #[Route('/', name: 'client_panier')]
    public function index(SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        return $this->render('client/panier.html.twig', [
            'panier' => $panier,
        ]);
    }

    #[Route('/ajouter/{id}', name: 'client_ajouter_panier')]
    public function ajouter(Produit $produit, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);
        $id = $produit->getId();

        if (!isset($panier[$id])) {
            $panier[$id] = [
                'nom' => $produit->getNom(),
                'prix' => $produit->getPrix(),
                'quantite' => 1
            ];
        } else {
            $panier[$id]['quantite']++;
        }

        $session->set('panier', $panier);
        $this->addFlash('success', 'Produit ajouté au panier !');

        return $this->redirectToRoute('client_catalogue');
    }

    #[Route('/supprimer/{id}', name: 'client_supprimer_panier')]
    public function supprimer(int $id, SessionInterface $session): Response
    {
        $panier = $session->get('panier', []);

        if (isset($panier[$id])) {
            unset($panier[$id]);
            $session->set('panier', $panier);
            $this->addFlash('info', 'Produit supprimé du panier.');
        }

        return $this->redirectToRoute('client_panier');
    }

    #[Route('/vider', name: 'client_vider_panier')]
    public function vider(SessionInterface $session): Response
    {
        $session->remove('panier');
        $this->addFlash('info', 'Panier vidé.');
        return $this->redirectToRoute('client_panier');
    }

    #[Route('/commander', name: 'client_commander')]
    public function commander(SessionInterface $session, EntityManagerInterface $em): Response
    {
        $panier = $session->get('panier', []);

        if (empty($panier)) {
            $this->addFlash('error', 'Votre panier est vide.');
            return $this->redirectToRoute('client_panier');
        }

        // ✅ Création de la commande
        $commande = new Commande();
        $commande->setDateCommande(new \DateTimeImmutable());
        $commande->setTotal(array_sum(array_map(fn($p) => $p['prix'] * $p['quantite'], $panier)));
        $commande->setClient($this->getUser());

        $em->persist($commande);
        $em->flush();

        // 🧹 Vide le panier après commande
        $session->remove('panier');
        $this->addFlash('success', 'Votre commande a été passée avec succès !');

        return $this->redirectToRoute('client_mes_commandes');
    }
}
