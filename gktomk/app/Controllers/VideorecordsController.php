<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\GroupsModel;
use GKTOMK\Models\VideorecordsModel;
use GKTOMK\Models\ZoomModel;

class VideorecordsController extends Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function main(){
        $VideorecordsModel = new VideorecordsModel();
        $logs = $VideorecordsModel->getAllRecords($_GET);
        $programs = (new GroupsModel())->getGroups();
        //print_r($logs);
        $this->View->setVar('LOGS', $logs);
        $this->View->setVar('PROGRAMS', $programs);
        $this->View->setVars($_GET);
        $this->View->parseTpl('videorecords', false)->parseTpl('main')->output();
    }

    public function getTest(){
        $VideorecordsModel = new VideorecordsModel();
        $res = $VideorecordsModel->downloadRecordById(1546);
        var_dump($res);
    }

    public function getTest2(){
        $VideorecordsModel = new VideorecordsModel();
        $result = $VideorecordsModel->cronStart();
        var_dump($result);
    }

    public function getRedownload(int $id){
        $VideorecordsModel = new VideorecordsModel();
        $VideorecordsModel->setStatusRecordById($id, 'redownload');
        $result = $VideorecordsModel->downloadRecordById($id);
        $record = $VideorecordsModel->getRecordById($id);
        return json_encode(['result' => $result, 'record' => $record]);
    }

    public function getView(int $lesson_id){

    }

    /*
     * Загрузить старые записи видео
     * */
    public function getOld(){
        $VideorecordsModel = new VideorecordsModel();
        $VideorecordsModel->getLoadOldLesson();
    }

}
