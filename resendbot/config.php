<?php
// Конфигурационный файл

$dataConfig['bot_api_key']  = '806573252:AAGvItV8WSN1Z1RnaNzyuJcpTwb858iesSg';
$dataConfig['bot_username'] = 'SystematikaAlertBot';



$dataConfig['admin_users'] = [
    907219811,
    1273288090,
	];



$dataConfig['commands_paths'] = [
    __DIR__ . '/app/Bots/Telegram/Commands/',
];


$dataConfig['mysql_credentials'] = [
    'host'     => 'localhost',
    'user'     => 'systematika_gktomk',
    'password' => 'vI4sQ7fR4gjG3i',
    'database' => 'systematika_resendbot',
];

//$dataConfig['mysql_credentials'] = [
//    'host'     => 'localhost',
//    'user'     => 'root',
//    'password' => '',
//    'database' => 'systematika_resendbot',
//];

$dataConfig['url_site'] = 'http://onlinekrasov.ru/p/resendbot';
$dataConfig['gk_url'] = 'https://online.systematika.org';


$dataConfig['support_group_id'] = -1001960985896;

/*define('C_REST_CLIENT_ID','local.5f5e0513664130.17602636');//Application ID
define('C_REST_CLIENT_SECRET','EWdcGlznnvuVBOk3o2b6jjilqVaCf4S5h27uCM2IsX722B984o');//Application key*/

define('CONFIG', $dataConfig);
