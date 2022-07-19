<?php

// Конфигурационный файл
// eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcGlLZXlJZCI6MjE1LCJzZXNzaW9uSWQiOiIwNTE4NzdjMS1jYTcxLTQ2YzAtYWE5OS1lZjQzZTU1MGQwZGYiLCJleHBpcmVzQXQiOiIyMDIwLTA4LTE0VDE1OjUzOjM1KzAwOjAwIiwiY29tcGFueUlkIjoxMjY4NywiaWF0IjoxNTk2ODE1NjE1LCJleHAiOjE1OTc0MjA0MTV9.H6NByqkivvN2hMYyKQBrO8h9NgXScpuE4KkFRlvLAhI
// API Ключ сервиса MoyKlass
$dataConfig['mk_api_key'] = 'M6M8PdBCjAvgAry1McKmiHrmrb0n6Wu6VbvFFcGt409nSaUQOP';

$dataConfig['mysql_credentials'] = [
    'host'     => 'localhost',
    'user'     => 'root',
    'database' => 'systematika_gktomk',
    'password' => '',
];

//$dataConfig['mysql_credentials'] = [
//    'host'     => 'localhost',
//    'user'     => 'systematika_gktomk',
//    'database' => 'systematika_gktomk',
//    'password' => 'vI4sQ7fR4gjG3i',
//];

$dataConfig['url_site'] = 'https://systematika.org/gktomk';
$dataConfig['tpl_name'] = 'default';
$dataConfig['url_gk'] = 'https://online.systematika.org';

// Какой курс разрешить включать в абонемент
//// Все группы этих курсов будут доступны пользователю
$dataConfig['offer_to_subscription'] = [
    '1031346' => '24747', // Абонемент 4 занятия, 2800
    '966110' => '24747', // Абонемент 4 занятия, 2800
    '970034' => '24748', // Пробный урок, 700
    '1047574' => '24739', // Абонемент 8 занятия, 5000
    '1054457' => '24739', // Абонемент 8 занятия, 5000
    '1047575' => '24867', // Абонемент 12 занятия, 7000
    '1054461' => '24867', // Абонемент 12 занятия, 7000
    '1054462' => '24740', // Абонемент 16 занятия, 8800
    '1047577' => '24740', // Абонемент 16 занятия, 8800
    '1223767' => '26186', // Абонемент 32 занятия, 16000
];

// Синхронизация абонементов



$dataConfig['admin_password'] = [
	'04d3ef88674a1a39d7659c5df4252d97', // Nekrasov
	'7798c59750d7022d9696a8b53c9f5473', // Михаил
	'f66261133865c888a1f41e1b0860b367', // Герберт
	'303f54813251651dc2e1f5d42464782a' // Настя Шугаева
	];


// Стартовая группа для всех новых пользователей
$dataConfig['startGroup'] = '122055';
// Нужно ли удалять из стартовой группы (во время добавления клиента в группу вручную)
$dataConfig['startGroup_delete'] = 1;
// Статусы записей в группы
$dataConfig['statusGroup'] = [
    'recorded' => '31034', // Для статуса "Записан"
    'learns' => '2', // Для статуса "Учится"
];
// Название поля (заголовок в гк) в ГК для добавления даты последнего посещения
$dataConfig['gk_field_date_last_lesson'] = 'Дата последнего посещения занятия';

// Название поля (заголовок в гк) в ГК для добавления даты последнего пробного посещения
$dataConfig['gk_field_date_last_test_lesson'] = 'Дата последнего пробного занятия';

// Название поля (заголовок в гк) в ГК для количества абонементов
$dataConfig['gk_field_count_user_subscriptions'] = 'Количество абонементов';

// Название поля (заголовок в гк) в ГК для количества абонементов
$dataConfig['gk_field_user_subscriptions_left_visits'] = 'Оставшееся количество посещений';

// Название поля (заголовок в гк) в ГК для количества абонементов индивидуальных
$dataConfig['gk_field_user_subscriptions_left_visits_individual'] = 'Оставшееся количество индивидуальных посещений';

// Название поля (заголовок в гк) в ГК для количества абонементов групповых
$dataConfig['gk_field_user_subscriptions_left_visits_group'] = 'Оставшееся количество групповых посещений';

// Название поля "даты пропуска пробного занятия"
$dataConfig['gk_field_date_missing_test'] = 'Дата пропуска пробного занятия';

// Название поля ближайшей платной записи
$dataConfig['gk_field_next_paid_recording'] = 'Ближайшая платная запись';

// Название поля ближайшей бесплатной записи
$dataConfig['gk_field_next_free_recording'] = 'Ближайшая бесплатная запись';

/// Настройки для домашних заданий
/// Интеграция с ГК
$dataConfig['gk_account_name'] = 'schoolmasters';
$dataConfig['gk_secret_key'] = '6hhwZl6P20739jcO18MDxVNn1mhyX8mmWyDRecocbeoWJxeE8D4rbRmnxXWngJPFTxlaTutimA10qTfRVcqTp4LkpbV34oPYPBRvIRakPoCuzCsH1FZcgxXgK6BvDsAm';

/// Префикс групп
$dataConfig['gk_prefix_group'] = 'Материалы занятий';

$dataConfig['zoom_api'] = [
    'key' => 'pfTr1IlDS6qnpHWAq5TR7A',
    'secret' => 'W000GLZCo2LZPbPrQB5Soec1SxCleZXl39RN',
];


$dataConfig['bot_api_key']  = '806573252:AAGvItV8WSN1Z1RnaNzyuJcpTwb858iesSg';
$dataConfig['bot_username'] = 'SystematikaAlertBot';
$dataConfig['support_group_id'] = -485700200;

define('CONFIG', $dataConfig);
