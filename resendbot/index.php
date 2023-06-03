<?php // Тестовый телеграм бот. Платная подписка на обучения. Мой ID TG: 907219811



// Load composer
require_once __DIR__ . '/vendor/autoload.php';
use \RedBeanPHP\R as R;


R::setup('mysql:host=localhost;dbname=tgedbot', 'tgedbot', 'tgedbot123');

// Подключаем конфиг
App\Config::init();

App\Models\Route\Route::init();




