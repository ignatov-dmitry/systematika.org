<?php // Тестовый телеграм бот. Платная подписка на обучения. Мой ID TG: 907219811
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Путь к файлу, в который будут записываться ошибки
$logFile = 'error_log.log';
// Включаем запись ошибок в файл
ini_set('log_errors', 1);
// Указываем путь к файлу для записи ошибок
ini_set('error_log', $logFile);
// Устанавливаем уровень отчетности об ошибках
error_reporting(E_ALL);


// Load composer
require_once __DIR__ . '/vendor/autoload.php';
use \RedBeanPHP\R as R;


R::setup('mysql:host=localhost;dbname=tgedbot', 'tgedbot', 'tgedbot123');

// Подключаем конфиг
App\Config::init();

App\Models\Route\Route::init();




