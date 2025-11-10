<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/user')]
class AdminUserController extends AbstractController
{
    #[Route('/', name: 'admin_user_index')]
    public function index(EntityManagerInterface $em): Response
    {
        // 🔒 Seul le SUPER_ADMIN peut accéder à cette page
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin_user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_user_edit')]
    public function edit(
        User $user,
        Request $request,
        EntityManagerInterface $em,
        MailerInterface $mailer
    ): Response {
        // 🔒 Protection stricte
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        // 🚫 Empêche le super admin de modifier son propre rôle
        if ($this->getUser()->getId() === $user->getId()) {
            $this->addFlash('warning', '⚠️ Vous ne pouvez pas modifier votre propre rôle.');
            return $this->redirectToRoute('admin_user_index');
        }

        // ✅ Formulaire soumis
        if ($request->isMethod('POST')) {
            // Récupération des rôles cochés ou valeur par défaut
            $roles = $request->request->all('roles') ?: ['ROLE_USER'];

            // ⚙️ Mise à jour
            $user->setRoles($roles);
            $em->flush();

            // ✉️ Email à l'utilisateur concerné
            $this->sendMail(
                $mailer,
                $user->getEmail(),
                '🔔 Mise à jour de votre rôle utilisateur',
                "
                <h2>Bonjour,</h2>
                <p>Votre rôle a été mis à jour par l’administrateur.</p>
                <p><strong>Nouveaux rôles :</strong> " . implode(', ', $roles) . "</p>
                <p>Merci de votre confiance.</p>
                <hr>
                <small>Application de gestion des ventes</small>
                "
            );

            // ✉️ Notification au super admin
            $this->sendMail(
                $mailer,
                'oumaimakammoun22@gmail.com',
                '🛠️ Modification de rôle effectuée',
                "
                <h2>Notification de changement de rôle</h2>
                <p>L’utilisateur <strong>" . $user->getEmail() . "</strong> a vu ses rôles mis à jour.</p>
                <p><strong>Nouveaux rôles :</strong> " . implode(', ', $roles) . "</p>
                <p>Effectué par : <strong>" . $this->getUser()->getEmail() . "</strong></p>
                <hr>
                <small>Application de gestion des ventes</small>
                "
            );

            // ✅ Confirmation utilisateur
            $this->addFlash('success', '✅ Rôles mis à jour et emails envoyés avec succès !');
            return $this->redirectToRoute('admin_user_index');
        }

        return $this->render('admin_user/edit.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * 📨 Méthode utilitaire pour centraliser l’envoi des emails
     */
    private function sendMail(MailerInterface $mailer, string $to, string $subject, string $htmlContent): void
    {
        try {
            $email = (new Email())
                ->from('oumaimakammoun22@gmail.com')
                ->to($to)
                ->subject($subject)
                ->html($htmlContent);

            $mailer->send($email);
        } catch (\Exception $e) {
            $this->addFlash('error', '⚠️ Erreur lors de l’envoi de l’email : ' . $e->getMessage());
        }
    }
}
