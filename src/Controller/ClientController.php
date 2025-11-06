<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/client')]
class ClientController extends AbstractController
{
    #[Route('/catalogue', name: 'client_catalogue')]
    public function catalogue(ProduitRepository $produitRepository): Response
    {
        return $this->render('client/catalogue.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/mes-commandes', name: 'client_mes_commandes')]
    public function mesCommandes(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();
        $commandes = $commandeRepository->findBy(['client' => $user]);

        return $this->render('client/mes_commandes.html.twig', [
            'commandes' => $commandes,
        ]);
    }

    #[Route('/profil', name: 'client_profil')]
    public function profil(): Response
    {
        return $this->render('client/profil.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
