<?php


namespace GKTOMK\Models;

/**
 * Модель обрабатывает заявку от клиента на отправку в МойКласс
 * */
class HandlerHwkModel extends HomeworkModel
{

    private $hwkData;
    private $hwkId;
    private $statusHandle = 'start';

    public function __construct()
    {
    }

    public function startHandlerByDate($dateStart, $dateFinish)
    {

        if (!$dateStart or !$dateFinish)
            return 'date error';

        //$data = ['date' => [$dateStart, $dateFinish]];

        $data['date'][] = $dateStart;
        //$data['date'][] = $dateFinish;

        // var_dump($data);

        $lessons = MoyklassModel::getLessons(['date' => [$dateStart, $dateFinish], 'includeRecords' => 'true']);

        if (!$lessons['lessons'])
            return 'not lessons';


        foreach ($lessons['lessons'] as $lesson) {
            //$class = MoyklassModel::getClassById($lesson['classId']);
            $groupGK = $this->findGroup($lesson['description']);

            if (!$groupGK)
                continue;


            $records = $lesson['records'];

            // var_dump($records);

            if (empty($records)) {
                continue;
            }


            foreach ($records as $record) {
                if ($record['visit'] < 1)
                    continue;

                $result = $this->createHwk([
                    'lessonRecordId' => $record['id'],
                    'userId' => $record['userId'],
                    'lessonId' => $record['lessonId'],
                    'visit' => $record['visit']
                ]);


                $this->cronHandle();

                usleep(100);

            }
        }

        // var_dump();

        //return $lessons;


    }

    /*
     * Считает сколько у клиетов осталось абонементов
     * */
    public function startCountUserSubscriptions()
    {
        session_start();
        if(!isset( $_SESSION['users_'])) $_SESSION['users_'] = [];


       // $users = $this->getUnicUsers();

        if(!isset($_SESSION['subscriptions'])) {
            $_SESSION['subscriptions'] = MoyklassModel::getUserSubscriptions(['statusId'=>'2']);
        }

            $subscriptions = $_SESSION['subscriptions'];



        //var_dump($subscriptions);


        $users_ = [];
        foreach ($subscriptions as $sub) {
            $users_[] = $sub['userId'];
        }


       // return;
        $users_ = array_diff($users_, $_SESSION['users_']);





        //var_dump($users_);


       $cnt = 0;

        if($users_>0){
            foreach ($users_ as $mk_user_id) {
                $userMk = MoyklassModel::getUserById(['userId' => $mk_user_id]);

                $GetcourseModel = new GetcourseModel();

                $dataCreate = [
                    'email' => $userMk['email'],
                ];


                // Обновляем количество абонементов у клиента
                $user_subscriptions = MoyklassModel::getUserSubscriptions(['userId' => $userMk['id'], 'statusId' => '2']);
                if (!empty($user_subscriptions['subscriptions'])) {
                    $dataCreate['count_user_subscriptions'] = $user_subscriptions['stats']['totalItems'];
                    $dataCreate['user_subscriptions_left_visits'] = ($user_subscriptions['stats']['totalVisits'] - $user_subscriptions['stats']['totalVisited']);
                }


                $result = $GetcourseModel->createUser($dataCreate);


              //  var_dump($result);


                $cnt++;
                $_SESSION['users_'][] = $mk_user_id;
                if($cnt==100) break;
            }
        }

        $left = count($users_) - count($_SESSION['users_']);
        echo 'Еще осталось: '.$left;


    }


    /*
     * Запускает обработку задачи
     * */

    public function cronHandle()
    {
        $Hwks = $this->getCronHwk();

        foreach ($Hwks as $Hwk) {

            echo $Hwk->id . ' загружен <br/>';
            echo '<br/> Статус обработки:' . $this->handle($Hwk->id)->getStatus();
        }
    }

    public function getStatus()
    {
        return $this->statusHandle;
    }


    /*
     * Обработка записи домашнего задания
     * */

    private function handle($hwkId)
    {
        $this->hwkId = $hwkId;
        $this->startHandler();
        return $this;
    }

    private function startHandler()
    {
        $this->hwkData = $this->getHwkById($this->hwkId);

        if (!$this->hwkData or !$this->hwkData['id']) {
            $this->resultHandle(['status' => 'error', 'code' => 'not found', 'text' => 'Не найдена запись лога домашнего задания!', 'debug' => $this->hwkData]);
            return 0;
        }
        //var_dump( $this->hwkData);


        $userMk = MoyklassModel::getUserById(['userId' => $this->hwkData['mk_user_id']]);

        if (!$userMk or !$userMk['id']) {
            $this->resultHandle(['status' => 'error', 'code' => 'mk user not found', 'text' => 'Пользователь МК не найден', 'debug' => [$this->hwkData, $userMk]]);
            return 0;
        }

        //var_dump($userMk);


       // $lesson = MoyklassModel::getLesson($this->hwkData['mk_lesson_id'], $this->hwkData['mk_user_id']);
        $lesson = MoyklassModel::getLessonById($this->hwkData['mk_lesson_id']);

        if (!$lesson and !$lesson['id']) {
            $this->resultHandle(['status' => 'error', 'code' => 'lesson not found', 'text' => 'Урок не найден', 'debug' => [$this->hwkData, $lesson]]);
            return 0;
        }

        $groupGK = $this->findGroup($lesson['description']);

        $GetcourseModel = new GetcourseModel();

        $dataCreate = [
            'email' => $userMk['email'],
            'group' => $groupGK,
        ];

/*
        // Обновляем дату последнего пробного
        $lesson_last_test = MoyklassModel::getLessonVisitLastTest($this->hwkData['mk_user_id']);
        if (isset($lesson_last_test) and !empty($lesson_last_test)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last_test['date']));
            $dataCreate['date_last_test_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $dataCreate['date_last_test_lesson'] = '01.01.1970';
        }

        // Дата последнего посещения урока
        $lesson_last = MoyklassModel::getLessonVisitLast($this->hwkData['mk_user_id']);
        if (isset($lesson_last) and !empty($lesson_last)) {
            $date_last_lesson = @date("d.m.Y", strtotime($lesson_last['date']));
            $dataCreate['date_last_lesson'] = $date_last_lesson;
        } else { // Если даты нет, ставим "пустое значение поля"
            $dataCreate['date_last_lesson'] = '01.01.1970';
        }

        // Обновляем количество абонементов у клиента
        $user_subscriptions = MoyklassModel::getUserSubscriptions(['userId' => $userMk['id'], 'statusId' => '2']);
        if (!empty($user_subscriptions['subscriptions'])) {
            $dataCreate['count_user_subscriptions'] = $user_subscriptions['stats']['totalItems'];
            $dataCreate['user_subscriptions_left_visits'] = ($user_subscriptions['stats']['totalVisits'] - $user_subscriptions['stats']['totalVisited']);
        }
*/

        $result = $GetcourseModel->createUser($dataCreate);

        if ($result and $result->success) {
            $this->resultHandle(['status' => 'success', 'code' => 'success', 'text' => 'Запись передана в ГК', 'debug' => [$result]]);
            $this->setHwk($this->hwkId, ['email' => $userMk['email'], 'gk_uid' => $result->result->user_id, 'group' => $groupGK]);
        } else {
            $this->resultHandle(['status' => 'error', 'code' => 'error update gk user', 'text' => 'Ошибка при создании пользователя в ГК', 'debug' => [$result]]);
        }


        // Отправка сообщений вотсап



        return $result;

    }

    private function resultHandle($result = [])
    {
        $this->setHwk($this->hwkId, ['status' => $result['status']]);
        if (isset($result['debug']) and is_array($result['debug']))
            $result['debug'] = json_encode($result['debug']);

        $this->addLogHwk($this->hwkId, @$result['code'], @$result['text'], @$result['debug']);
        $this->statusHandle = $result['status'];
        return 1;
    }

    private function sendWhatsapp(){

    }


}