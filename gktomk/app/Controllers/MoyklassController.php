<?php

namespace GKTOMK\Controllers;

use GKTOMK\Models\HomeworkModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\HandlerHwkModel;

class MoyklassController extends Controller
{

    /**
     * @var HomeworkModel
     */
    private $Hwk;

    function __construct()
    {
        $this->Hwk = new HomeworkModel();

    }

    public function main()
    {
        $input = json_decode(
            file_get_contents('php://input'), true
        );
        $this->writeToLog($input, 'Дебаг MK. Получение данных - php/input', 'mk');

        if (!empty($input['event']) and ($input['event'] == 'lesson_record_new' or $input['event'] == 'lesson_record_changed') and ($input['object']['visit'] and $input['object']['visit']==1)) {
            $this->Hwk->createHwk($input['object']);

            // Запускаем обработку
            $HandlerHwkModel = new HandlerHwkModel();
            $HandlerHwkModel->cronHandle();
        }

    }

    public function getLog(){

    }

    public function getTest()
    {



        $HandlerHwkModel = new HandlerHwkModel();

        $res = $HandlerHwkModel->cronHandle();
        var_dump($res);
    }


}