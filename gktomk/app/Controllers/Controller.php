<?php

namespace GKTOMK\Controllers;

use GKTOMK\Models\MemberModel;

class Controller
{

    private $timer;
    /**
     * @var MemberModel
     */
    protected $Member;
    /**
     * @var \GKTOMK\Views\IndexView
     */
    protected $View;

    public function __construct()
    {
        $this->Member = new MemberModel();
        $this->Member->session_start();

        $this->View = new \GKTOMK\Views\IndexView();
        $this->View->setVar('URL_GK', CONFIG['url_gk']);

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header("Access-Control-Allow-Headers: X-Requested-With");

    }

    public function answerAjax($data)
    {
        exit(json_encode($data));
    }

    public function writeToLog($data, $title = '', $logFile = 'log')
    {
//        $log = "\n------------------------\n";
//        $log .= date("Y.m.d G:i:s") . "\n";
//        $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
//        if (is_array($data)) $log .= print_r($data, 1);
//        else $log .= $data;
//
//        $log .= "\n------------------------\n";
//        file_put_contents(__DIR__ . '/../../logs/' . $logFile . '.log', $log, FILE_APPEND);
//        return true;
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