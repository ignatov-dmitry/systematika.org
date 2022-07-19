<?php
// Модель для работы с API мой класс

namespace GKTOMK\Models;

use GKTOMK\Config;

class MoyklassModel
{
    /**
     * @var string
     */
    private static $urlApi = 'https://api.moyklass.com'; // Адрес сервера апи
    private static $versionApi = 'v1'; // Версия API к которой мы будем обращаться
    private static $accessKeyApi;
    private static $accessTokenApi;


    public function __construct()
    {
        self::init();
    }

    public static function init()
    {
        if(empty(CONFIG)) Config::init();
        if(empty(self::$accessKeyApi)) self::$accessKeyApi = CONFIG['mk_api_key'];
        if(empty(self::$accessTokenApi)) self::getToken(); // Получаем вначале токен

    }

    /*
     * Получаем токен для дальнейшего использования
     * */
    private static function getToken()
    {
        $result = self::callCurlAPI('company/auth/getToken', ['apiKey' => self::$accessKeyApi]);
        self::$accessTokenApi = $result['accessToken'];
        return $result['accessToken'];
    }

    /*
     * Вызывает URL API MoyClass curl
     * */
    private function callCurlAPI($url = '', $data = array(), $method = 'POST')
    {
        if ($method == 'GET') $curl_method = CURLOPT_HTTPGET;
        elseif ($method == 'POST') $curl_method = CURLOPT_POST;

        if (!empty($data) and $method == 'GET') {
            $query = http_build_query($data);
            $url .= '?' . $query;
        }
        //var_dump($url);

        $curl = curl_init(self::$urlApi . '/' . self::$versionApi . '/' . $url);

        curl_setopt($curl, CURLOPT_USERAGENT, 'Integration by NekrasovOnline.RU');
        curl_setopt($curl, $curl_method, 1);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        //curl_setopt($curl, CURLOPT_VERBOSE, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);

        // $authorization = "Authorization: Bearer " . $access_token; // Prepare the authorisation token
        // curl_setopt($curl, CURLOPT_HTTPHEADER, array($authorization)); // Inject the token into the header

        /*        $authorization = "Authorization: Bearer ".$access_token; // Prepare the authorisation token
                curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
                curl_setopt($curl, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($query),
                    $authorization
                ]);
                */

        if (!empty(self::$accessTokenApi)) $headers[] = 'x-access-token: ' . self::$accessTokenApi;
        if (!empty($data) and $method == 'POST') {
            $headers[] = 'Content-Type: application/json';
            $query = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
            $headers[] = 'Content-Length: ' . strlen($query);

        }

        if (!empty($headers)) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);


        //print_r($headers);


        $body = curl_exec($curl);


        $result['status_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result['body'] = $body;
        curl_close($curl);
        $data = json_decode($body, 1);
        return $data;
    }
    /*
     * Производит запуск модели
     * */
    private static function startApi($url = '', $data = array(), $method = 'POST')
    {

        self::init();

        if(empty(self::$accessKeyApi)){
            return 'Error access key api';
        }elseif(empty(self::$accessTokenApi)){
            return 'Error access token api';
        }

        return self::callCurlAPI($url, $data, $method);
    }

    /*
     * Ищем юзера по MK ID
     * */

    public static function getUserById($filter = ['userId' => ''])
    {
        return self::startApi('company/users/' . $filter['userId'], '', 'GET');
    }

    /*
     * Ищем юзеров используя фильтр по емейлу и/или телефону (фильтр можно указать любой исходя из документации по апи МК)
     * */
    public static function getFindUsers($filter = ['email' => '', 'phone' => ''])
    {
        return self::startApi('company/users/', $filter, 'GET');
    }

    /*
     * Возвращает список абонементов ученика
    */

    public static function getUserSubscriptions($filter = ['userId' => ''])
    {
        return self::startApi('company/userSubscriptions', $filter, 'GET');
    }

    /*
     * Создает пользователя в МойКласс
     * */
    public static function createUser($data = ['name', 'email'])
    {
        return self::startApi('company/users/', $data, 'POST');
    }

    /*
     * Редактирует пользователя в МойКласс
     * */
    public static function editUserById($userId, $data = ['name', 'email'])
    {

    }

    /*
     * Возвращает список классов (группы)
     * */
    public static function getClasses()
    {
        return self::startApi('company/classes', '', 'GET');
    }

    /**
     * Возвращает список уроков по фильтру или без него
     * */
    public static function getLessons($data = []){
        return self::startApi('company/lessons', $data, 'GET');
    }


    /*
     * Создает новый абонемент пользователю
     * subscriptionId - ID абонемента который подключить пользователю
     * */
    public static function createUserSubscriptions($data = ['userId', 'subscriptionId', 'sellDate', 'classIds', 'mainClassId'])
    {
        return self::startApi('company/userSubscriptions', $data, 'POST');
    }

    /*
     * Изменение статуса абонемента пользователя
     * statusId - enum (1 - active, 2 - inactive)
     * */
    public static function setStatusUserSubscriptions($userSubscriptionId, $statusId)
    {
        return self::startApi('company/userSubscriptions/' . $userSubscriptionId . '/status', '', 'POST');
    }

    /*
     * Получает список всех абонементов компании
     * */
    public static function getSubscriptions($filter = [])
    {
        return self::startApi('company/subscriptions', $filter, 'GET');
    }

    /*
     * Получает информацию о конкретном абонементе
     * */
    public static function getSubscription($subscription_id)
    {
        return self::startApi('company/subscriptions/'.$subscription_id,'', 'GET');
    }

    /*
     * Создает платеж для пользователя
     * optype = income - приход, debit - списание, refund - возврат
     * */
    public static function createPaymentUser($data = ['userId', 'date', 'summa', 'optype']){
        return self::startApi('company/payments', $data, 'POST');
    }

    /**
     * Получает счета
     * */
    public static function getInvoices($filter = []){
        return self::startApi('company/invoices', $filter, 'GET');
    }
}