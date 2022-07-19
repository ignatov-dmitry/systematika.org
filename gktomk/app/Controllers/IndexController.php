<?php

namespace GKTOMK\Controllers;
//use \GKTOMK\Lib\Controller;

//use \GKTOMK\Views\IndexView;
use GKTOMK\Models\HandlerModel;
use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\LogsModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\SyncModel;

class IndexController extends Controller
{

    public $View = '';


    function __construct()
    {
        session_start();
        if(!empty($_GET['password']))
            $_SESSION['passwords'] = $_GET['password'];
        if(isset($_REQUEST['password']) and $_REQUEST['password'] != CONFIG['admin_password']){
            die('Access error!');
        }
        $this->View = new \GKTOMK\Views\IndexView();

        $this->View->setVar('URL_GK', CONFIG['url_gk']);
    }

    function main()
    {


        $LogsModel = new LogsModel();
        $all_users = $LogsModel->buildLogs();

        // Регистрируем функции, чтобы можно было вызвать их в шаблоне
        $this->View->setVar('Logs',  $LogsModel);
        $this->View->regFunc('GKTOMK\Models\LogsModel::timeFormat');


        $this->View->setVar('LOGS', $all_users);



        // Вызываем шаблонизатор
        $this->View->parseTpl('logs', false)->parseTpl('main')->output();
    }


    public function getSettConn()
    {

        $this->View->parseTpl('settings/connection', false)->parseTpl('main')->output();
    }

    public function getSettSync()
    {

       // SyncModel::getSyncAll();

        $SyncModel = new SyncModel();
       // $SyncModel->createSync('Абонемент 4 занятия, 2800', '1031346', '24747');

        $syncs = $SyncModel->getAllSync();

        var_dump($syncs);
        $this->View->setVar('SYNCS', $syncs);

        $this->View->parseTpl('settings/synchronization', false)->parseTpl('main')->output();
    }

    public function postSettSyncAdd(){
        print_r($_REQUEST);
    }

    public function getSubscriptions()
    {
        $this->View->varTpl('SUBSCRIPTIONS', MoyklassModel::getSubscriptions()['subscriptions']);
	    $this->View->parseTpl('settings/subscriptions', false)->parseTpl('main')->output();
    }

    public function getMkClasses(){

        $classes = MoyklassModel::getClasses();

        // Убираем из списка группы которые находятся в архвие
        for($i=0; $i<count($classes); $i++){
            if($classes[$i]['status']==="archive"){
                unset($classes[$i]);
                $classes[$i] = null;
            }

        }
        //var_dump($classes);
       return json_encode($classes);
    }

    public function getMkCourses(){
        return json_encode(MoyklassModel::getCourses(['includeClasses'=>'true']));
    }

    public function postAddclass(){
       // var_dump($_REQUEST);

        $addclass = $_POST['addclass'];

        if(empty($addclass['classId'])){
            $this->answerAjax(['status'=> 'error', 'data'=>'empty classId']);
        }

        if(empty($addclass['userEmail'])){
            $this->answerAjax(['status'=> 'error', 'data'=>'empty userEmail']);
        }


        $LeadsModel = new LeadsModel();
        $mk_user = $LeadsModel->getFindUserByEmail($addclass['userEmail']);

        if(empty($mk_user) or empty($mk_user['id'])){
            $this->answerAjax(['status'=> 'error', 'data'=>'user not found']);
        }


        // Проверяем, есть ли клиент в стартовой группе и нужно ли его удалять оттуда
        if(!empty(CONFIG['startGroup']) and CONFIG['startGroup'] > 0 and CONFIG['startGroup_delete']){
            $start_join = 0;
            foreach($mk_user['joins'] as $join){
                if($join['classId']==CONFIG['startGroup']){
                    $start_join = $join['id'];
                }
            }
            if($start_join > 0){
                // Удаляем из стартовой группы
                MoyklassModel::deleteJoins(['joinId' => $start_join]);
            }
        }




        // Преобразуем в массив, если он не был таким
        if(isset($addclass['classId']) and !is_array($addclass['classId'])) $addclass['classId'] = [$addclass['classId']];

        foreach($addclass['classId'] as $classid){
           // $results[] = $classid;
            $result = MoyklassModel::setJoins(['userId'=>$mk_user['id'], 'classId'=> intval($classid), 'statusId' => 2]);
           if(isset($result['id']) and !empty($result['id'])){ // Успешно создано
                $answer = ['status'=>'success', 'data'=>$result];
            } else { // Произошла какая-то ошибка
                $answer = ['status'=>'error', 'data'=>$result];
            }

            $results[] = $answer;
        }

       // $result = MoyklassModel::setJoins(['userId'=>$mk_user['id'], 'classId'=> intval($addclass['classId']), 'statusId' => 2]);

        $this->answerAjax(['results'=>@$results]);

    }

    public function getTest(){

       // $HandlerModel = new HandlerModel();

      //  var_dump($HandlerModel->handle(27));

       // $result = MoyklassModel::getCourses(['includeClasses'=>'true']);

        $mk_user = MoyklassModel::getFindUsers(['email' => 'anekrasov123@mail.ru'])['users'][0];

        /*//var_dump($mk_user);
        $start_join = 0;
        foreach($mk_user['joins'] as $join){
            if($join['classId']=='122055'){
                $start_join = $join['id'];
            }
        }
        echo 'Start join ID: '.$start_join;

        $result = MoyklassModel::deleteJoins(['joinId' => $start_join]);*/
        var_dump($mk_user);

       // $class = MoyklassModel::getClasses();

        //var_dump($class);

       // $lessons = MoyklassModel::getLessons(['classId'=>'107265'])['lessons'];
       // $lessons = MoyklassModel::getLessons(['userId'=>834428])['lessons'];

        /*foreach($lessons as $lesson){
            echo $lesson['id'].'<br/>';
        }*/


       // var_dump($lessons);

      // $result = MoyklassModel::setJoins(['userId'=>834428, 'classId'=> intval('107265'), 'statusId' => 2]);

        //$result =  MoyklassModel::getCreateSources();
       // var_dump($result);


       // $LeadsModel->addLogUser(27, 'test', 'Тестовый лог', 'Информация для дебага');


       //var_dump($LeadsModel->getLogUser('27'));

       // $LeadsModel->delLogUser(27);

    }



}