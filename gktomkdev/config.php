<?php

// Конфигурационный файл
// eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJhcGlLZXlJZCI6MjE1LCJzZXNzaW9uSWQiOiIwNTE4NzdjMS1jYTcxLTQ2YzAtYWE5OS1lZjQzZTU1MGQwZGYiLCJleHBpcmVzQXQiOiIyMDIwLTA4LTE0VDE1OjUzOjM1KzAwOjAwIiwiY29tcGFueUlkIjoxMjY4NywiaWF0IjoxNTk2ODE1NjE1LCJleHAiOjE1OTc0MjA0MTV9.H6NByqkivvN2hMYyKQBrO8h9NgXScpuE4KkFRlvLAhI
// API Ключ сервиса MoyKlass
$dataConfig['mk_api_key'] = 'M6M8PdBCjAvgAry1McKmiHrmrb0n6Wu6VbvFFcGt409nSaUQOP';

$dataConfig['mysql_credentials'] = [
    'host'     => 'localhost',
    'user'     => 'root',
    'password' => '',
    'database' => 'systematika_gktomk',
];


$dataConfig['url_site'] = 'https://systematika.org/gktomkdev';
$dataConfig['tpl_name'] = 'default';
$dataConfig['url_gk'] = 'https://online.systematika.org';

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




/// Настройки для домашних заданий
/// Интеграция с ГК
$dataConfig['gk_account_name'] = 'schoolmasters';
$dataConfig['gk_secret_key'] = '6hhwZl6P20739jcO18MDxVNn1mhyX8mmWyDRecocbeoWJxeE8D4rbRmnxXWngJPFTxlaTutimA10qTfRVcqTp4LkpbV34oPYPBRvIRakPoCuzCsH1FZcgxXgK6BvDsAm';

/// Префикс групп 
$dataConfig['gk_prefix_group'] = 'Материалы занятий';


$dataConfig['admin_password'] = [
    '123',
    '04d3ef88674a1a39d7659c5df4252d97',
];

$dataConfig['zoom_api'] = [
    'key' => 'pfTr1IlDS6qnpHWAq5TR7A',
    'secret' => 'W000GLZCo2LZPbPrQB5Soec1SxCleZXl39RN',
];


$dataConfig['bot_api_key']  = '806573252:AAGvItV8WSN1Z1RnaNzyuJcpTwb858iesSg';
$dataConfig['bot_username'] = 'SystematikaAlertBot';
$dataConfig['support_group_id'] = -485700200;



define('CONFIG', $dataConfig);