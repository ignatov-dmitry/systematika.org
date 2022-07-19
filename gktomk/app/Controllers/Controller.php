<?php

namespace GKTOMK\Controllers;

class Controller {

    public function answerAjax($data){
        exit(json_encode($data));
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