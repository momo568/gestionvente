<?php

namespace App\Controller;

use App\Repository\ProduitRepository;
use App\Repository\CommandeRepository;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/', name: 'app_dashboard')]
    public function index(ProduitRepository $produitRepo, CommandeRepository $commandeRepo, ClientRepository $clientRepo): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'nbProduits' => count($produitRepo->findAll()),
            'nbCommandes' => count($commandeRepo->findAll()),
            'nbClients' => count($clientRepo->findAll()),
        ]);
    }
}
