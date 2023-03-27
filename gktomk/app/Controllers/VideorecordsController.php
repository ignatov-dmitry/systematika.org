<?php


namespace GKTOMK\Controllers;


use GKTOMK\Classes\Pagination;
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
        $args = $_GET;
        $args['page'] = '{page}';
        $VideorecordsModel = new VideorecordsModel();


        $pagination = new Pagination();
        $pagination->total = $VideorecordsModel->getCountRecords($args);
        $pagination->page = $_GET['page'] ?: 1;
        $pagination->limit = 10;
        $pagination->url = 'videorecords?' . http_build_query($args);

        $args['limit'] = $pagination->limit;
        $args['offset'] = $pagination->limit * ($pagination->page - 1);

        $logs = $VideorecordsModel->getAllRecords($args);

        $programs = (new GroupsModel())->getGroups();
        //print_r($logs);
        $this->View->setVar('LOGS', $logs);
        $this->View->setVar('PROGRAMS', $programs);
        $this->View->setVar('PAGINATION', $pagination->render());
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
