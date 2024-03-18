<?php
// Модель для работы с API мой класс

namespace GKTOMK\Models;

use GKTOMK\Config;
use GKTOMK\Models\GetCourse\User;

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
    private static function callCurlAPI($url = '', $data = [], $method = 'POST')
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

    private static function startApi($url = '', $data = array(), $method = 'POST')
    {

        $time_run_start = microtime(true);

        self::init();

        if (empty(self::$accessKeyApi)) {
            return 'Error access key api';
        } elseif (empty(self::$accessTokenApi)) {
            return 'Error access token api';
        }

        $i = 0;
        $results = [];
        do{
            $result = self::callCurlAPI($url, $data, $method);
            $results[$i] = $result;
            if(!empty($result['code']) and $result['code']=='TooManyRequests'){
                 usleep(rand(250, 500));
                $i = $i + 1;
            }else
                 break;
        }while($i<5);

        self::setLogRequest([
            'time' => time(),
            'time_run' => round(microtime(true) - $time_run_start, 5),
            'time_run_global' => round(microtime(true) - GLOBAL_TIMER, 5),
            'rounds' => $i,
            'url' => $url,
            'method' => $method,
            'data' => json_encode($data),
            'results' => json_encode($results),
            '_request' => json_encode($_REQUEST),
            '_server' => json_encode($_SERVER),
            '_server_request_uri' => $_SERVER['REQUEST_URI'],
            '_server_http_referer' => @$_SERVER['HTTP_REFERER'],
        ]);

        return $result;

    }

    public static function setLogRequest($dataLog = []){
        //// Сохраняем в лог
        //DB::init();
        // $url = '', $data = array(), $method = 'POST'
        //DB::edit('logmoyklass', $dataLog);
    }

    /*
    * Производит запуск модели
    * */

    public static function getUserByEmail($email)
    {
        $finds = self::getFindUsers(['email' => $email]);

        // var_dump($finds);

        if (!isset($finds) or $finds['stats']['totalItems'] < 1)
            return 0;

        if ($finds['stats']['totalItems'] == 1) {
            return $finds['users'][0];
        } else if ($finds['stats']['totalItems'] > 1) {
            foreach ($finds['users'] as $user) {
                if ($user['email'] == $email) {
                    return $user;
                }
            }
        }

        return 0;
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
        return self::startApi('company/users/' . $userId, $data);
    }

    public static function getFindUserByEmail($email)
    {
        $mk_user = self::getFindUsers(['email' => $email]);
        // Если поиск по юзерам вернул больше 1 значения, тогда ищем первое наиболее подходящее

        if (!empty($mk_user['users']) and count($mk_user['users']) > 1) {

            foreach ($mk_user['users'] as $user) {
                if ($user['email'] == $email) {
                    return $user;
                }
            }
            $mk_user = NULL; // Пользователь не найден с указанным email
        } else {
            $mk_user = @$mk_user['users'][0];
        }
        return $mk_user;
    }

    /*
    * Возвращает список программ
     * includeClasses = включает в ответ список групп (классов)
    * */

    public static function getCourseById($courseId)
    {

        if (!isset(self::$cache['courses']['time']) or (self::$cache['courses']['time'] + 300) < time()) {
            self::$cache['courses']['time'] = time();
            self::$cache['courses']['data'] = self::getCourses();
        }

        $courses = self::$cache['courses']['data'];

        foreach ($courses as $course) {
            if ($courseId and $courseId == $course['id']) {
                return $course;
            }
        }
    }

    public static function getCourses($data = ['includeClasses' => 'false'])
    {
        return self::startApi('company/courses', $data, 'GET');
    }

    /*
     * Возвращает список классов (группы)
     * */

    public static function getClassById($classId)
    {

        if (!isset(self::$cache['classes']['time']) or (self::$cache['classes']['time'] + 300) < time()) {
            self::$cache['classes']['time'] = time();
            self::$cache['classes']['data'] = self::getClasses();
        }

        $classes = self::$cache['classes']['data'];

        foreach ($classes as $class) {
            if ($classId and $classId == $class['id']) {
                return $class;
            }
        }
    }

    public static function getClasses()
    {
        return self::startApi('company/classes', '', 'GET');
    }

    public static function getClassByIdMK($classId)
    {
        return self::startApi('company/classes/' . $classId, '', 'GET');
    }

    public static function getManagers()
    {
        return self::startApi('company/managers', '', 'GET');
    }

    public static function getManagersAssoc()
    {
        $teachers = self::getManagers();
        $teachersAssoc = [];
        foreach ($teachers as $teacher) {
            $teachersAssoc[$teacher['id']] = $teacher;
        }
        return $teachersAssoc;
    }


    public static function getManagerById($managerId)
    {
        return self::startApi('company/managers/' . $managerId, '', 'GET');
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

    /**
     * Возвращает список уроков по фильтру или без него
     * @param array $data
     * @return array|mixed|string
     */
    public static function getLessons($data = [])
    {
        return self::startApi('company/lessons', $data, 'GET');
    }

    public static function getLessonById($lesson_id, $filter = [])
    {
        return self::startApi('company/lessons/' . $lesson_id, $filter, 'GET');
    }

    public static function getLessonRecord($record_id)
    {
        return self::startApi('company/lessonRecords/' . $record_id, [], 'GET');
    }

    public static function getUserLessonRecords($user_id)
    {
        return self::startApi('company/lessonRecords/', ['userId' => $user_id, 'limit' => 500], 'GET');
    }

    public static function setLessonRecord($record_id, $data = [])
    {
        return self::startApi('company/lessonRecords/' . $record_id, $data, 'POST');
    }

    public static function deleteLessonRecord($record_id, $data = [])
    {
        return self::startApi('company/lessonRecords/' . $record_id, $data, 'DELETE');
    }


    /**
     * Возвращает ближайшее платное и бесплатное занятие
     */
    public static function getNextPaidAndFreeRecording($userId)
    {
        $lessons = self::getLessons(['userId' => $userId, 'includeRecords' => 'true', 'date[0]' => date('Y-m-d'), 'date[1]' => date('Y-m-d', strtotime('+1 year'))]);
        $lessons = $lessons['lessons'];

        $nextDatePaid = '9999-12-31';
        $nextDateFree = '9999-12-31';

        for ($i = 0; $i < count($lessons); $i++) {

            $lesson = $lessons[$i];
            $records = $lesson['records'];


            if (date('Y-m-d H:m') > $lesson['date'].' '.$lesson['beginTime']) {
                continue;
            }

            foreach ($records as $record) {
                if ($record['free'] == false && $nextDatePaid > $lesson['date']) {
                    $nextDatePaid = $lesson['date'];
                    //break;
                }

            }

            foreach ($records as $record) {
                if ($record['free'] == true && $nextDatePaid > $lesson['date']) {
                    $nextDateFree = $lesson['date'];
                    //break;
                }

            }
        }


        $nextDatePaid = $nextDatePaid !== '9999-12-31' ? (new \DateTime($nextDatePaid))->format('d.m.Y') : '';
        $nextDateFree = $nextDateFree !== '9999-12-31' ? (new \DateTime($nextDateFree))->format('d.m.Y') : '';


        return ['date_next_paid_lesson' => $nextDatePaid, 'date_next_free_lesson' => $nextDateFree];
    }

    /**
     * Возвращает дату последнего и пробного занятия
     *
     * */
    public static function getLessonVisitLastByUserId($userId)
    {
        $lessons = self::getLessons(['userId' => $userId, 'includeRecords' => 'true']);

        //print_r($lessons);

        $lessons = $lessons['lessons'];
        $dataLesson = [];
        $dataLessonTest = [];
        $maxTimeLesson = 0;
        $maxTimeLessonTest = 0;
        for ($i = 0; $i < count($lessons); $i++) {
            $lesson = $lessons[$i];
            //echo '123';
            $records = $lesson['records'];

            foreach ($records as $record) {
                if ($record['userId'] == $userId and $record['visit'] == true) {

                    /// Вычисляем последнее занятие
                    $dateLesson = strtotime($lesson['date']);
                    if ($maxTimeLesson < $dateLesson) {
                        $maxTimeLesson = $dateLesson;
                        $dataLesson = $lesson;
                    }
                    /// Вычисляем занятие тестовое
                    if ($record['userId'] == $userId and $record['visit'] == 1 and $record['test'] == 1) {
                        $dateLessonTest = strtotime($lesson['date']);
                        if ($maxTimeLessonTest < $dateLessonTest) {
                            $maxTimeLessonTest = $dateLessonTest;
                            $dataLessonTest = $lesson;
                        }
                    }

                }

            }
        }


        return ['date_last_test_lesson' => $dataLessonTest, 'date_last_lesson' => $dataLesson];
    }

    /**
     * Возвращает последнее занятие для пользователя
     *
     * @param $userId
     * @return mixed
     */
    public static function getLessonVisitLast($userId)
    {
        $lessons = self::getLessons(['userId' => $userId, 'includeRecords' => 'true']);

        // var_dump($lessons);

        $lessons = $lessons['lessons'];
        $dataLesson = [];
        $maxTimeLesson = 0;
        for ($i = 0; $i < count($lessons); $i++) {
            $lesson = $lessons[$i];
            //echo '123';
            $records = $lesson['records'];

            foreach ($records as $record) {
                if ($record['userId'] == $userId and $record['visit'] == true) {

                    $dateLesson = strtotime($lesson['date']);
                    if ($maxTimeLesson < $dateLesson) {
                        $maxTimeLesson = $dateLesson;
                        $dataLesson = $lesson;
                    }

                }

            }
        }


        return $dataLesson;
    }

    /**
     * Возвращает последнее занятие для пользователя
     *
     * @param $userId
     * @return mixed
     */
    public static function getLessonSkipLast($userId)
    {
        $lessons = self::getLessons(['userId' => $userId, 'includeRecords' => 'true', 'date[0]' => '01.01.1970', 'date[1]' => date("Y-m-d")]);

        // var_dump($lessons);

        $lessons = $lessons['lessons'];
        $dataLesson = [];
        $maxTimeLesson = 0;
        for ($i = 0; $i < count($lessons); $i++) {
            $lesson = $lessons[$i];
            //echo '123';
            $records = $lesson['records'];

            foreach ($records as $record) {
                if ($record['userId'] == $userId and $record['visit'] == false) {

                    $dateLesson = strtotime($lesson['date']);
                    if ($maxTimeLesson < $dateLesson) {
                        $maxTimeLesson = $dateLesson;
                        $dataLesson = $lesson;
                    }

                }

            }
        }


        return $dataLesson;
    }

    /**
     * Возвращает последнее пробное занятие для пользователя
     *
     * @param $userId
     * @return mixed
     */
    public static function getLessonVisitLastTest($userId)
    {
        $lessons = self::getLessons(['userId' => $userId, 'includeRecords' => 'true']);
        $lessons = $lessons['lessons'];

        $dataLesson = [];
        $maxTimeLesson = 0;
        for ($i = 0; $i < count($lessons); $i++) {
            $lesson = $lessons[$i];

            $records = $lesson['records'];

            foreach ($records as $record) {
                if ($record['userId'] == $userId and $record['visit'] == 1 and $record['test'] == 1) {
                    $dateLesson = strtotime($lesson['date']);
                    if ($maxTimeLesson < $dateLesson) {
                        $maxTimeLesson = $dateLesson;
                        $dataLesson = $lesson;
                    }
                }

            }
        }
        return $dataLesson;
    }

    /** Возвращает следующее платное и бесплатное занятие для каждого ученика
     * @return array
     */
    public static function getUsersNextFreeAndPaidLessons(): array
    {
        $count = 0;
        $lessons = array();
        $users = array();

        do {
            $lessonsRequest = self::getLessons([
                'includeRecords' => 'true',
                'date[0]'        => date('Y-m-d'),
                'date[1]'        => date('Y-m-d', strtotime('+1 year')),
                'limit'          => 500,
                'offset'         => $count
            ]);

            $totalItems = $lessonsRequest['stats']['totalItems'];

            $count += count($lessonsRequest['lessons']);

            $lessons = array_merge($lessons, $lessonsRequest['lessons']);

        } while($totalItems <> $count);


        foreach ($lessons as $lesson) {
            foreach ($lesson['records'] as $record) {

                if ($record['free'] == false) {
                    if (!isset($users[$record['userId']]['date_next_paid_lesson'])) {
                        $users[$record['userId']]['date_next_paid_lesson'] = $lesson['date'];
                    }

                    else {
                        $users[$record['userId']]['date_next_paid_lesson']
                            = date('Y-m-d', strtotime($users[$record['userId']]['date_next_paid_lesson'])) < date('Y-m-d', strtotime($lesson['date']))
                            ? $users[$record['userId']]['date_next_paid_lesson'] : $lesson['date'];
                    }
                }

                if ($record['free'] == true) {
                    if (!isset($users[$record['userId']]['date_next_free_lesson'])) {
                        $users[$record['userId']]['date_next_free_lesson'] = $lesson['date'];
                    }

                    else {
                        $users[$record['userId']]['date_next_free_lesson']
                            = date('Y-m-d', strtotime($users[$record['userId']]['date_next_free_lesson'])) < date('Y-m-d', strtotime($lesson['date']))
                            ? $users[$record['userId']]['date_next_free_lesson'] : $lesson['date'];
                    }
                }
            }
        }

        return $users;
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
     * Редактирует заявку (запись) в группу
     * @param array $data
     * @return array|mixed|string
     */
    public static function editJoins($userId, $data = ['price', 'statusId', 'statusChangeReasonId', 'autoJoin', 'comment', 'advSourceId', 'createSourceId'])
    {
        return self::startApi('company/joins/'.$userId, $data, 'POST');
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
     * Получает заявки
     * @param array $data
     * @return array|mixed|string
     */
    public static function getJoins($data = ['classId', 'userId'])
    {
        return self::startApi('company/joins', $data, 'GET');
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

    /**
     * [Справочник] Получение аттрибутов (полей) пользователя
     * */
    public static function getUserAttributes()
    {
        return self::startApi('company/userAttributes', '', 'GET');
    }


}
