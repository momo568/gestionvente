<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Dotenv\Dotenv;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function boot(): void
    {
        // ✅ Charger le .env.local avant tout
        $dotenv = new Dotenv();
        $dotenv->loadEnv(dirname(__DIR__).'/.env.local');

        parent::boot();
    }
}
