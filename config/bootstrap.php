<?php

use Symfony\Component\Dotenv\Dotenv;

require dirname(__DIR__).'\/vendor/autoload.php';

// ✅ Charge le .env puis .env.local automatiquement
if (file_exists(dirname(__DIR__).'/.env')) {
    (new Dotenv())->loadEnv(dirname(__DIR__).'/.env');
}
