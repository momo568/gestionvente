<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\Promotion;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaiementController extends AbstractController
{
    #[Route('/client/paiement/{id}', name: 'client_paiement', methods: ['GET', 'POST'])]
    public function payer(Request $request, Commande $commande, EntityManagerInterface $em): Response
    {
        if ($commande->getClient() !== $this->getUser()) {
            throw $this->createAccessDeniedException('Vous ne pouvez pas payer cette commande.');
        }

        $action = $request->request->get('action');
        $codePromo = strtoupper(trim($request->request->get('code_promo')));
        $now = new \DateTime('now');

        if ($request->isMethod('POST')) {
            if ($action === 'apply_code' && $codePromo) {
                $promotion = $em->getRepository(Promotion::class)->findOneBy(['code' => $codePromo]);

                if (!$promotion) {
                    $this->addFlash('error', '❌ Code promo invalide.');
                    return $this->redirectToRoute('client_paiement', ['id' => $commande->getId()]);
                }

                if ($promotion->getDateDebut() > $now) {
                    $this->addFlash('error', '⏳ Ce code promo n’est pas encore actif.');
                    return $this->redirectToRoute('client_paiement', ['id' => $commande->getId()]);
                }

                if ($promotion->getDateFin() < (clone $now)->setTime(23, 59, 59)) {
                    $this->addFlash('error', '⚠️ Ce code promo est expiré.');
                    return $this->redirectToRoute('client_paiement', ['id' => $commande->getId()]);
                }

                $remise = ($commande->getTotal() * $promotion->getPourcentage()) / 100;
                $commande->setTotal($commande->getTotal() - $remise);
                $em->flush();

                $this->addFlash('success', '🎉 Code promo appliqué : réduction de ' . $promotion->getPourcentage() . '% !');
                return $this->redirectToRoute('client_paiement', ['id' => $commande->getId()]);
            }

            if ($action === 'pay') {
                $commande->setStatut('payée');
                $em->flush();

                $this->addFlash('success', '✅ Paiement effectué avec succès !');
                return $this->redirectToRoute('client_mes_commandes');
            }
        }

        return $this->render('client/paiement.html.twig', [
            'commande' => $commande,
        ]);
    }
}
