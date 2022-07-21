<?php

namespace GKTOMK\Controllers;

use GKTOMK\Models\GetCourse\User;
use GKTOMK\Models\GetcourseModel;
use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\MissingTrialModel;
use GKTOMK\Models\MoyklassModel;

class GetcourseController extends Controller
{

    function __construct()
    {
        parent::__construct();
    }

    public function main()
    {
         self::writeToLog($_REQUEST, 'Дебаг GK. Получение данных');
        // Принимаем данные и сохраняем пользователя
        $request = $_REQUEST;
        if (!empty($request['uid'])) {

            $LeadsModel = new LeadsModel();

            // Сохраняем данные которые к нам пришли в виде данных для отправки в мк
            $LeadsModel->createUser($request);

            // Обновляем данные пользователя в нашей базе
            $member_id = $this->Member->updateMemberByGkUhash($request);
            //$this->Member->sendMemberToMoyKlassById($member_id);

            // Сразу запрашиваем их обработку
            $LeadsModel->cronHandlerUsers();

            // Запрашиваем обновление данных для пользователя
            $this->getUpdateUser($request['email']);


        }
    }

    /*
     * Метод позволяет заправшивать обновление для конкретного пользователя в гк
     *
     * */
    public function getUpdateUser($email)
    {
        $GetCourse = new GetcourseModel();
        $GetCourse->updateUserSubscriptions($email)
            ->updateUserDateVisit($email)
            ->sendUser();
        echo 'OK ' . $email;
    }

    public function getCreateMember(){
        $member_id = $this->Member->updateMemberByGkUhash([
            'gk_uid' => $_GET['uid'],
            'gk_uhash' => $_GET['gk_uhash'],
            'first_name' => $_GET['first_name'],
            'last_name' => $_GET['last_name'],
            'email' => $_GET['email'],
            'phone' => $_GET['phone'],
        ]);
        $this->Member->sendMemberToMoyKlassById($member_id);
    }

    public function getGiveAccess(){
        $this->Member->updateMemberByGkUhash([
            'gk_uhash' => $_GET['gk_uhash'],
            'access' => $_GET['access'],
        ]);
    }

    /*
     * Метод позволяет заправшивать обновление для конкретного пользователя в гк
     *
     * */
    public function getUpdateUserByIdUserMk($userId)
    {

        $user = MoyklassModel::getUserById(['userId' => $userId]);
        $email = $user['email'];
        $GetCourse = new GetcourseModel();
        return $GetCourse->updateUserSubscriptions($email)
            ->updateUserDateVisit($email)
            ->sendUser();
        echo 'OK ' . $email .' ';
    }

    /*
     * Проверка пробных песещений для занятия
     * */
    public function getUpdateLesson(){
        $lesson = MoyklassModel::getLessonById(5524226, ['includeRecords' => 'true']);

        var_dump($lesson);
    }

    /* Обновляет всех клиентов из моего класса
     *
     * */

    public function getUpdateAllMkUsers(){
        session_start();



        if(!isset($_SESSION['lessons']) or isset($_GET['new'])){
            $_SESSION['lessons'] = MoyklassModel::getLessons(['date' => ['2021-03-01', '2021-03-16'], 'includeRecords' => 'true']);
        }
        $lessons = $_SESSION['lessons'];

       // var_dump($lessons);

        $users = [];
        foreach ($lessons['lessons'] as $lesson) {
            foreach ($lesson['records'] as $record) {
                if($record['visit']==true)
                    $users[] = $record['userId'];
            }
        }

        $users = array_unique($users);

        echo count(array_unique($users)) . PHP_EOL;
        //$_SESSION['users'] = [];
        if(!isset( $_SESSION['users'])) $_SESSION['users'] = [];

        $_SESSION['users'] = array_unique($_SESSION['users']);
        $users = array_diff($users, $_SESSION['users']);

        $num = 1;
        foreach ($users as $user) {
            $res = $this->getUpdateUserByIdUserMk($user);
            $this->writeToLog([$res], '', 'updateMK');
            //var_dump($res);

            echo $num . '. User Id: '.$user.' update<br/>'. PHP_EOL;
            $num++;
            $_SESSION['users'][] = $user;
            if($num==10){ echo '<meta http-equiv="refresh" content="5">'; break; }
            usleep(250);
        }

        $left = count($users) - count($_SESSION['users']);
        echo count($users) . PHP_EOL;
        echo count(array_unique($_SESSION['users']));
        echo 'Еще осталось: '.$left;
        
        
    }

    public function writeToLog($data, $title = '', $logFile = 'log')
    {
        $log = "\n------------------------\n";
        $log .= date("Y.m.d G:i:s") . "\n";
        $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
        if (is_array($data)) $log .= print_r($data, 1);
        else $log .= $data;

        $log .= "\n------------------------\n";
        file_put_contents(__DIR__ . '/../../logs/' . $logFile . '.log', $log, FILE_APPEND);
        return true;
    }

    public function getTest(){
        $Missing = new MissingTrialModel();

       // $Missing->addMissing(5524226);
        $miss = $Missing->handleMissings();

       var_dump($miss);
    }

    public function getUpdateNextLessons()
    {
        $users = MoyklassModel::getUsersNextFreeAndPaidLessons();

        foreach ($users as $key => $user) {
            $User = new User();
            $User::setAccountName(CONFIG['gk_account_name']);
            $User::setAccessToken(CONFIG['gk_secret_key']);


            $userMk = MoyklassModel::getUserById(['userId' => $key]);

            $User = $User->setEmail($userMk['email'])
                ->setOverwrite();

            $dateNextPaidLesson = isset($user['date_next_paid_lesson']) ? (new \DateTime($user['date_next_paid_lesson']))->format('d.m.Y') : '';
            $dateNextFreeLesson = isset($user['date_next_free_lesson']) ? (new \DateTime($user['date_next_free_lesson']))->format('d.m.Y') : '';

            $User->setUserAddField(CONFIG['gk_field_next_paid_recording'], $dateNextPaidLesson);
            $User->setUserAddField(CONFIG['gk_field_next_free_recording'], $dateNextFreeLesson);


            try {
                $result = $User->apiCall($action = 'add');
            } catch (\Exception $e) {
                $result = $e->getMessage();
                var_dump($result);
                die();
            }
        }
    }

}