<?php

namespace GKTOMK\Controllers;

use GKTOMK\Models\GetcourseModel;
use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\MissingTrialModel;
use GKTOMK\Models\MoyklassModel;

class GetcourseController
{

    function __construct()
    {

    }

    public function main()
    {
         self::writeToLog($_REQUEST, 'Дебаг GK. Получение данных');
        // Принимаем данные и сохраняем пользователя
        $request = $_REQUEST;
        if (!empty($request['uid'])) {
            $LeadsModel = new LeadsModel();

            // Сохраняем данные которые к нам пришли в виде пользователя
            $LeadsModel->createUser($request);

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
        $GetCourse->updateUserSubscriptions($email);
        $GetCourse->updateUserDateVisit($email);
        echo 'OK ' . $email;
    }

    /*
     * Проверка пробных песещений для занятия
     * */
    public function getUpdateLesson(){
        $lesson = MoyklassModel::getLessonById(5524226, ['includeRecords' => 'true']);

        var_dump($lesson);
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


}