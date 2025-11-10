<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;



namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailTestController extends AbstractController
{
    #[Route('/test-mail', name: 'app_test_mail')]
    public function sendMail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('oumaimakammoun22@gmail.com')
            ->to('oumaima.kammoun@sesame.com.tn') // tu peux mettre n'importe quelle adresse ici
            ->subject('✅ Test d’envoi d’e-mail avec Symfony')
            ->text('Ceci est un test d’envoi d’e-mail depuis Symfony avec Gmail.')
            ->html('<p><strong>Ceci est un test réussi !</strong><br>Envoyé via <em>Symfony Mailer</em>.</p>');

        try {
            $mailer->send($email);
            return new Response('✅ E-mail envoyé avec succès ! Vérifie ta boîte Gmail.');
        } catch (\Exception $e) {
            return new Response('❌ Erreur : ' . $e->getMessage());
        }
    }
}
