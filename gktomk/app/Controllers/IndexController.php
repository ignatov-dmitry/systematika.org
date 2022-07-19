<?php

namespace GKTOMK\Controllers;
//use \GKTOMK\Lib\Controller;

//use \GKTOMK\Views\IndexView;
use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\LogsModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\StatisticsModel;
use GKTOMK\Models\SyncModel;

class IndexController extends Controller
{

    public $View = '';


    function __construct()
    {
        session_start();
        if (!empty($_GET['password']))
            $_SESSION['password'] = $_GET['password'];
        if (isset($_REQUEST['password']) and $_REQUEST['password'] != CONFIG['admin_password']) {
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
        $this->View->setVar('Logs', $LogsModel);
        $this->View->regFunc('GKTOMK\Models\LogsModel::timeFormat');


        $this->View->setVar('LOGS', $all_users);


        // Вызываем шаблонизатор
        $this->View->parseTpl('logs', false)->parseTpl('main')->output();
    }

    public function getHomework()
    {
        $LogsModel = new LogsModel();
        $all_hwk = $LogsModel->buildLogsHwk();

        $this->View->setVar('Logs', $LogsModel);
        $this->View->regFunc('GKTOMK\Models\LogsModel::timeFormat');

        // var_dump($all_hwk);
        $this->View->setVar('LOGS', $all_hwk);

        // Вызываем шаблонизатор
        $this->View->parseTpl('logs_hwk', false)->parseTpl('main')->output();
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

    public function postSettSyncAdd()
    {
        print_r($_REQUEST);
    }

    public function getSubscriptions()
    {
        $this->View->varTpl('SUBSCRIPTIONS', MoyklassModel::getSubscriptions()['subscriptions']);
        $this->View->parseTpl('settings/subscriptions', false)->parseTpl('main')->output();
    }

    public function getMkClasses()
    {

        $classes = MoyklassModel::getClasses();

        // Убираем из списка группы которые находятся в архвие
        for ($i = 0; $i < count($classes); $i++) {
            if ($classes[$i]['status'] === "archive") {
                unset($classes[$i]);
                $classes[$i] = null;
            }

        }
        //var_dump($classes);
        return json_encode($classes);
    }

    public function getMkcourse()
    {
        $time = time();
        $period = $time + (60 * 60 * 24 * 7); // Обновляем на 7 дней вперед
        $datestart = date("Y-m-d", $time);
        $dateend = date("Y-m-d", $period);

        $stat = new StatisticsModel();
        $stat->getLoadVisits($datestart, $dateend);


        $courses = MoyklassModel::getCourses(['includeClasses' => 'true']);
        $stats = new StatisticsModel();
        $data = [];


        //var_dump($courses);

        // Составляем массив данных в удобном формате
        foreach ($courses as $cours) {

            if ($cours['name'] == 'Индивидуальное обучение')
                continue;

            //$data[$cours['name']]['positions'] = ['1' => 0, '2' => 0, '3' => 0];

            foreach ($cours['classes'] as $class) {

                if ($class['status'] !== 'opened')
                    continue;

                $val = [
                    'id' => $class['id'],
                    'name' => $class['name'],
                    'stats' => array_reverse($stats->getVisitsLessonByClassId($class['id'])),
                ];

                $expName = explode("-", $class['name']);

                $expName[0] = preg_replace("/[^0-9]/", '', $expName[0]);


                if (isset($expName[2])) {
                    $data[$cours['name']][(int)$expName[0]][(int)$expName[2]][(int)$expName[1]] = $val;
                    ksort($data[$cours['name']][(int)$expName[0]]);
                    ksort($data[$cours['name']][(int)$expName[0]][(int)$expName[2]]);
                    ksort($data[$cours['name']][(int)$expName[0]][(int)$expName[2]][(int)$expName[1]]);
                } elseif (isset($expName[1])) {
                    // echo (int)$expName[0];
                    $data[$cours['name']][(int)$expName[0]][1][(int)$expName[1]] = $val;
                    ksort($data[$cours['name']][(int)$expName[0]]);
                } elseif (isset($expName[0])) {


                }


            }

            /// Делаем сортировку есть в программе есть группы
            if (isset($data[$cours['name']]))
                ksort($data[$cours['name']]);

        }


        $POSITION = [];
        // Разбираем массив и формируем список групп


        $numProgram = 0;
        foreach ($data as $nameProgram => $p1) {

            // Если в программе нет групп - не показываем ее
            if (!isset($p1))
                continue;

            $numProgram++;

            echo '<div id="accordion">
  <div class="card">
    <div class="card-header" id="heading' . $numProgram . '">
      <h5 class="mb-0">
        <button class="btn btn-link" data-toggle="collapse" data-target="#collapse' . $numProgram . '" aria-expanded="false" aria-controls="collapse' . $numProgram . '">
          ' . $nameProgram . '
        </button>
      </h5>
    </div>

    <div id="collapse' . $numProgram . '" class="collapse" aria-labelledby="heading' . $numProgram . '" data-parent="#accordion">
      <div class="card-body">
        ';


            echo '<table class="table table-sm table-bordered table-responsive " border="1">';
            $block = '0';
            foreach ($p1 as $p1key => $p1value) {

                if ($p1key == 'position')
                    continue;


                foreach ($p1value as $p2key => $p2value) {

                    if (isset($p2value['name'])) {
                        continue;
                    }

                    if (!is_array($p2value))
                        continue;


                    $td = "";
                    foreach ($p2value as $p3key => $p3value) {


                        $statText = '';
                        $numStat = 0;
                        $statTime = '';
                        foreach ($p3value['stats'] as $stat) {

                            $numStat++;
                            $statText .= ' ' . $stat['num_records'];

                            if (isset($stat['num_visits'])) {
                                $statText .= '(' . $stat['num_visits'] . ')';
                            }

                            $statTime = $stat['begin_time'];
                        }


                        $statTime = substr($statTime, 0, -3);
                        $text = "<a href='#' onclick='addclass.add.send(\"{$p3value['id']}\");'><span><b>{$p3value['name']}</b> {$statTime}</span><br/><span>{$statText}</span></a>";


                        $td .= "<td>$text</td>";
                    }

                    if (!empty($td)) {
                        echo "<tr>$td</tr>";
                    }


                }

                if ((int)$block < (int)$p1key) {
                    $block = $p1key;
                    echo "<tr><th><hr/></th></tr>";
                }

            }
            echo '</table>
            </div>
                </div>
              </div>
';


        }


        // ksort($data['Олимпиадная математика']);
        // print_r($data['ТРИЗ']);

        //  return json_encode($data);
    }

    public function getMkCourses()
    {
        return json_encode(MoyklassModel::getCourses(['includeClasses' => 'true']));
    }

    public function postAddclass()
    {
        // var_dump($_REQUEST);

        $addclass = $_POST['addclass'];

        if (empty($addclass['classId'])) {
            $this->answerAjax(['status' => 'error', 'data' => 'empty classId']);
        }

        if (empty($addclass['userEmail'])) {
            $this->answerAjax(['status' => 'error', 'data' => 'empty userEmail']);
        }


        $LeadsModel = new LeadsModel();
        $mk_user = $LeadsModel->getFindUserByEmail($addclass['userEmail']);

        if (empty($mk_user) or empty($mk_user['id'])) {
            $this->answerAjax(['status' => 'error', 'data' => 'user not found']);
        }


        // Проверяем, есть ли клиент в стартовой группе и нужно ли его удалять оттуда
        if (!empty(CONFIG['startGroup']) and CONFIG['startGroup'] > 0 and CONFIG['startGroup_delete']) {
            $start_join = 0;
            foreach ($mk_user['joins'] as $join) {
                if ($join['classId'] == CONFIG['startGroup']) {
                    $start_join = $join['id'];
                }
            }
            if ($start_join > 0) {
                // Удаляем из стартовой группы
                MoyklassModel::deleteJoins(['joinId' => $start_join]);
            }
        }


        // Преобразуем в массив, если он не был таким
        if (isset($addclass['classId']) and !is_array($addclass['classId'])) $addclass['classId'] = [$addclass['classId']];

        foreach ($addclass['classId'] as $classid) {
            // $results[] = $classid;
            $result = MoyklassModel::setJoins(['userId' => $mk_user['id'], 'classId' => intval($classid), 'statusId' => 2, 'autoJoin' => true]);
            if (isset($result['id']) and !empty($result['id'])) { // Успешно создано
                $answer = ['status' => 'success', 'data' => $result];
            } else { // Произошла какая-то ошибка
                $answer = ['status' => 'error', 'data' => $result];
            }

            $results[] = $answer;
        }

        // $result = MoyklassModel::setJoins(['userId'=>$mk_user['id'], 'classId'=> intval($addclass['classId']), 'statusId' => 2]);

        $this->answerAjax(['results' => @$results]);

    }

    public function getTest()
    {

        // $HandlerModel = new HandlerModel();

        //  var_dump($HandlerModel->handle(27));

        // $result = MoyklassModel::getCourses(['includeClasses'=>'true']);

        // $mk_user = MoyklassModel::getFindUsers(['email' => 'anekrasov123@mail.ru'])['users'][0];

        /*//var_dump($mk_user);
        $start_join = 0;
        foreach($mk_user['joins'] as $join){
            if($join['classId']=='122055'){
                $start_join = $join['id'];
            }
        }
        echo 'Start join ID: '.$start_join;

        $result = MoyklassModel::deleteJoins(['joinId' => $start_join]);*/
        ////  var_dump($mk_user);

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
        $datestart = '2020-10-10';
        $dateend = '2020-11-07';

        $stat = new StatisticsModel();
        $stat->getLoadVisits($datestart, $dateend);


    }


}