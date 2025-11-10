<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EnvTestController extends AbstractController
{
    #[Route('/check-env', name: 'check_env')]
    public function index(): Response
    {
        $dsn = $_ENV['MAILER_DSN'] ?? 'Variable non trouvée';
        return new Response('MAILER_DSN = ' . $dsn);
    }
}
