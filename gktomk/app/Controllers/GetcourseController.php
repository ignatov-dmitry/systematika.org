<?php

namespace GKTOMK\Controllers;

use GKTOMK\Models\LeadsModel;

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
            $LeadsModel->createUser($request);
        }
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


}