<?php

namespace App\Controller;

use App\Entity\Commande;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    #[Route('/client/paiement/{id}', name: 'client_paiement')]
    public function payer(Commande $commande, EntityManagerInterface $em): Response
    {
        // Vérifie que la commande appartient bien à l'utilisateur connecté
        if ($commande->getClient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas payer cette commande.');
        }

        // Met à jour le statut
        $commande->setStatut('payée');
        $em->flush();

        $this->addFlash('success', '✅ Votre paiement a été effectué avec succès !');
        return $this->redirectToRoute('client_mes_commandes');
    }
}
