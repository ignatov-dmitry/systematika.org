<?php

namespace App\Controllers;

class Controller
{

    private $timer;

    public function answerAjax($data)
    {
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


    /*
     * Метод позволяет считать время выполнения кусков кода
     * */
    public function genTime($name)
    {
        if(!isset($this->timer[$name]))
            $this->timer[$name] = microtime(true);
        else if ($this->timer[$name] and !empty($this->timer[$name])) {
            $time = round(microtime(true) - $this->timer[$name], 4);
            unset($this->timer[$name]);
            return $time;
        }
        return 1;
    }


}