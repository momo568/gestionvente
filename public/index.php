<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

// ✅ Charger manuellement les variables du .env.local
(new Dotenv())->loadEnv(dirname(__DIR__).'/.env.local');

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
