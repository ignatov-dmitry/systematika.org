<?php

namespace GKTOMK\Controllers;

use GKTOMK\Controllers\Controller;
use GKTOMK\Models\CancelLessonModel;
use GKTOMK\Models\TplfunctionsModel;

class CancellessonController extends Controller
{

    public $View = '';


    function __construct()
    {
        parent::__construct();
        $this->Member->is_auth();

        $this->View = new \GKTOMK\Views\IndexView();

    }

    public function main()
    {

        // Устанавливаем уровень доступа
        $this->Member->isAccess(1, true);

        $CancelLessonModel = new CancelLessonModel();
        $output = $CancelLessonModel->buildLogs();


        $Tplfunctions = new TplfunctionsModel();
        $this->View->setVar('Tplfunctions', $Tplfunctions);
        $this->View->regFunc('GKTOMK\Models\TplfunctionsModel::timeFormat');



        $this->View->setVar('LOGS', $output);

        // Вызываем шаблонизатор
        $this->View->parseTpl('cancellesson', false)->parseTpl('main')->output();
    }

    public function postAjaxCancelSave(){
        $data = [
            'id' => $_POST['id'],
            'type' => $_POST['type'],
            'status_adm' => $_POST['status_adm'],
            'comment' => $_POST['comment'],
        ];

        $CancelLessonModel = new CancelLessonModel();
        $CancelLessonModel->setCancel($data);
    }

}