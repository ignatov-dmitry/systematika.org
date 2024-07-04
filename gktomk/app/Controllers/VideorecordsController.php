<?php


namespace GKTOMK\Controllers;


use GKTOMK\Classes\Pagination;
use GKTOMK\Models\GroupsModel;
use GKTOMK\Models\Systematika\Model;
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

        foreach ($logs as $key => $log)
        {
            $logs[$key]['unassigned'] = false;
            $logs[$key]['modify'] = false;
            $path = DIR_PATH . '/videorecord/' . str_replace('-', '/', $log['date']) . '/' . $log['class_name'];
            $modifyPath = DIR_PATH . '/videorecord/' . str_replace('-', '/', $log['date']) . '/' . $log['meeting_topic'];

            $unassignedPath = DIR_PATH . '/videorecord/unassigned_videos/' . str_replace('-', '/', $log['date']) . '/' . $log['meeting_topic'];

            if (glob($path . '*.{mp4,MP4}', GLOB_BRACE))
                $logs[$key]['path'] = $path;
            elseif (glob($modifyPath . '*.{mp4,MP4}', GLOB_BRACE)) {
                $logs[$key]['path'] = $modifyPath;
                $logs[$key]['modify'] = true;
            }
            elseif (glob($unassignedPath . '*.{mp4,MP4}', GLOB_BRACE)) {
                $logs[$key]['path'] = $unassignedPath;
                $logs[$key]['unassigned'] = true;
                $logs[$key]['modify'] = true;
            }
            else
                $logs[$key]['path'] = false;

            $logs[$key]['meeting_topic'] = htmlspecialchars($log['meeting_topic']);
        }

        $programs = (new GroupsModel())->getGroups();
        //print_r($logs);
        $this->View->setVar('LOGS', $logs);
        $this->View->setVar('PROGRAMS', $programs);
        $this->View->setVar('PAGINATION', $pagination->render());
        $this->View->setVars($_GET);
        $this->View->parseTpl('videorecords', false)->parseTpl('main')->output();
    }

    public function getNotMatchedVideos()
    {
        $args = $_GET;
        $args['page'] = '{page}';
        $zoomModel = new ZoomModel();

        $criteria = [
            array('key' => 'zmr.download_status', 'val' => 'downloaded', 'op' => Model::OP_EQUAL),
            array('key' => 'zmr.zoom_status', 'val' => 'completed', 'op' => Model::OP_EQUAL),
            'file_extension' => 'MP4'
        ];

        $pagination = new Pagination();
        $pagination->total = $zoomModel->getCountZoomMeetings($criteria, 'start_time DESC', 20);
        $pagination->page = $_GET['page'] ?: 1;
        $pagination->limit = 20;
        $pagination->url = 'not-matched-videos?' . http_build_query($args);

        $offset = $pagination->limit * ($pagination->page - 1);
        //$programs = (new GroupsModel())->getGroups();
        $zoomVideos = $zoomModel->getZoomMeetings($criteria, 'start_time DESC', 20, $offset);



        $this->View->setVar('ZOOM_RECORDS', $zoomVideos);
        //$this->View->setVar('PROGRAMS', $programs);
        $this->View->setVar('PAGINATION', $pagination->render());
        $this->View->setVars($_GET);
        $this->View->parseTpl('zoomrecords', false)->parseTpl('main')->output();
    }

    public function getVideoFolder() {
        $path = $_GET['path'];
        $files = array();
        $unassignedPath = DIR_PATH . '/videorecord/' . $path;
        if($handle = opendir($unassignedPath)) {

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $files[] = array(
                        'path'      => $file,
                        'is_file'   => is_file($unassignedPath . '/' . $file),
                        'file_name' => htmlspecialchars(str_replace(['.mp4', '.MP4'], '', $file))
                    );
                }
            }
        }
        asort($files);
        $pathArray = explode('/', $path);
        array_pop($pathArray);

        $backUri = implode('/', $pathArray) ?: '/';
        $this->View->setVar('BACK', $backUri);
        $this->View->setVar('PATH', $path ? $path . '/' : '/');
        $this->View->setVar('ITEMS', $files);
        $this->View->parseTpl('folder')->output();
    }

    public function postSetTopicName()
    {
        $VideorecordsModel = new VideorecordsModel();
        $VideorecordsModel->setMeetingTopicName($_POST['record_id'], $_POST['name'], $_POST['file_path']);
        return json_encode($_POST);
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

    public function getSafe(int $id)
    {
        $VideorecordsModel = new VideorecordsModel();
        $VideorecordsModel->setSafe($id, 1);
    }
}
