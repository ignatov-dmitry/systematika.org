<?php
// Модель для работы с API мой класс

namespace GKTOMK\Models;

use GKTOMK\Config;

class MoyklassModel
{
    private static $cache = [];
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
        if (empty(CONFIG)) Config::init();
        if (empty(self::$accessKeyApi)) self::$accessKeyApi = CONFIG['mk_api_key'];
        if (empty(self::$accessTokenApi)) self::getToken(); // Получаем вначале токен

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


        if (!empty($data) and $method == 'GET') {
            $query = urldecode(http_build_query($data));
            $url .= '?' . $query;

            $url = str_replace('date[0]', 'date[]', $url);
            $url = str_replace('date[1]', 'date[]', $url);
        }
        //var_dump($url);

        $url = self::$urlApi . '/' . self::$versionApi . '/' . $url;

        //print($url);

        $curl = curl_init($url);

        curl_setopt($curl, CURLOPT_USERAGENT, 'Integration by NekrasovOnline.RU');

        if ($method == 'GET') curl_setopt($curl, CURLOPT_HTTPGET, 1);
        elseif ($method == 'POST') curl_setopt($curl, CURLOPT_POST, 1);
        else if ($method == 'DELETE') curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");


        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HEADER, 0);

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_TIMEOUT, 120);

        if (!empty(self::$accessTokenApi)) $headers[] = 'x-access-token: ' . self::$accessTokenApi;
        if (!empty($data) and ($method == 'POST' or $method == 'DELETE')) {
            $headers[] = 'Content-Type: application/json';
            $query = json_encode($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $query);
            $headers[] = 'Content-Length: ' . strlen($query);
        }

        if (!empty($headers)) curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $body = curl_exec($curl);

        $result['status_code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $result['body'] = $body;
        curl_close($curl);
        $data = json_decode($body, 1);
        return $data;
    }



    public static function getUserById($filter = ['userId' => ''])
    {
        return self::startApi('company/users/' . $filter['userId'], '', 'GET');
    }

    /*
     * Находит пользователя по email
     * */
    public static function getUserByEmail($email)
    {
        $finds = self::getFindUsers(['email' => $email]);

        if(!isset($finds) or $finds['stats']['totalItems'] < 1)
            return 0;

        if($finds['stats']['totalItems'] == 1){
            return $finds['users'][0];
        }else if($finds['stats']['totalItems'] > 1){
            foreach ($finds as $find) {
                if($find['email'] == $email){
                    return $find;
                }
            }
        }

        return 0;
    }

    /*
    * Производит запуск модели
    * */

    private static function startApi($url = '', $data = array(), $method = 'POST')
    {

        self::init();

        if (empty(self::$accessKeyApi)) {
            return 'Error access key api';
        } elseif (empty(self::$accessTokenApi)) {
            return 'Error access token api';
        }

        return self::callCurlAPI($url, $data, $method);
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
    * Возвращает список программ
     * includeClasses = включает в ответ список групп (классов)
    * */
    public static function getCourses($data = ['includeClasses' => 'false'])
    {
        return self::startApi('company/courses', $data, 'GET');
    }

    /*
     * Возвращает список классов (группы)
     * */
    public static function getClasses()
    {
        return self::startApi('company/classes', '', 'GET');
    }

    public static function getClassById($classId){

        if(!isset(self::$cache['classes']['time']) or (self::$cache['classes']['time'] + 300) < time()){
            self::$cache['classes']['time'] = time();
            self::$cache['classes']['data'] = self::getClasses();
        }

        $classes = self::$cache['classes']['data'];

        foreach ($classes as $class) {
            if($classId and $classId==$class['id']){
                return $class;
            }
        }
    }

    /**
     * Возвращает конкретное занятие с фильтром
     * @param $lessonId
     * @param array $data
     * @return array|mixed|string
     */
    public static function getLesson($lessonId, $userId = 0)
    {

        $data = [];

        if (!empty($userId))
            $data['userId'] = $userId;

        $lessons = self::getLessons($data);
        If (!empty($lessons)) {
            foreach ($lessons['lessons'] as $lesson) {
                if (isset($lesson['id']) and $lesson['id'] == $lessonId) {
                    return $lesson;
                }
            }
        }

        return 0;
    }

    public static function getLessonById($lesson_id, $filter = []){
        return self::startApi('company/lessons/' . $lesson_id, $filter, 'GET');
    }

    /**
     * Возвращает последнее занятие для пользователя
     *
     * @param $userId
     * @return mixed
     */
    public static function getLessonVisitLast($userId){
        $lessons = self::getLessons(['userId'=>$userId, 'includeRecords' => 'true']);
        $lessons = $lessons['lessons'];
        for ($i=count($lessons)-1;$i>=0; $i--) {
            $lesson = $lessons[$i];

            $records = $lesson['records'];

            foreach ($records as $record) {
                if($record['userId'] == $userId and $record['visit']==1)
                    return $lesson;
            }
        }
    }

    /**
     * Возвращает последнее пробное занятие для пользователя
     *
     * @param $userId
     * @return mixed
     */
    public static function getLessonVisitLastTest($userId){
        /*$res = self::getLessons(['userId'=>$userId]);
        if(function_exists('array_key_last')){
            $last = array_key_last($res['lessons']);
            $last_lesson = $res['lessons'][$last];
        }else{
            $last = end(array_keys($res['lessons']));
            $last_lesson = $res['lessons'][$last];
        }
        return $last_lesson;*/
        $lessons = self::getLessons(['userId'=>$userId, 'includeRecords' => 'true']);
        $lessons = $lessons['lessons'];


        for ($i=count($lessons)-1;$i>=0; $i--) {
            $lesson = $lessons[$i];

            $records = $lesson['records'];

            foreach ($records as $record) {
                if($record['userId'] == $userId and $record['visit']== 1 and $record['test'] == 1)
                    return $lesson;
            }
        }
    }

    /**
     * Возвращает список уроков по фильтру или без него
     * @param array $data
     * @return array|mixed|string
     */
    public static function getLessons($data = [])
    {
        return self::startApi('company/lessons', $data, 'GET');
    }

    /**
     * Создает новую запись на занятие
     *
     * @param array $data
     * @return array|mixed|string
     */
    public static function setLessonRecords($data = ['userId', 'lessonId'])
    {
        return self::startApi('company/lessonRecords', $data, 'POST');
    }

    /**
     * Создает новую заявку (запись) в группу
     * @param array $data
     * @return array|mixed|string
     */
    public static function setJoins($data = ['userId', 'classId', 'statusId', 'autoJoin' => true])
    {
        return self::startApi('company/joins', $data, 'POST');
    }

    /**
     * Удаляет заявку (запись) в группу
     * @param array $data
     * @return array|mixed|string
     */
    public static function deleteJoins($data = ['joinId'])
    {
        return self::startApi('company/joins/' . $data['joinId'], '', 'DELETE');
    }

    /**
     * Справочник. Возвращает список возможных способов заведения клиентов и заявок
     *
     * */
    public static function getCreateSources()
    {
        return self::startApi('company/createSources', '', 'GET');
    }

    /**
     * Создает новый абонемент пользователю
     * subscriptionId - ID абонемента который подключить пользователю
     * @param array $data
     * @return array|mixed|string
     */
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
        return self::startApi('company/subscriptions/' . $subscription_id, '', 'GET');
    }

    /*
     * Создает платеж для пользователя
     * optype = income - приход, debit - списание, refund - возврат
     * */
    public static function createPaymentUser($data = ['userId', 'date', 'summa', 'optype'])
    {
        return self::startApi('company/payments', $data, 'POST');
    }

    /**
     * Получает счета
     * */
    public static function getInvoices($filter = [])
    {
        return self::startApi('company/invoices', $filter, 'GET');
    }



}