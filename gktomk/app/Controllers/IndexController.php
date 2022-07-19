<?php

namespace GKTOMK\Controllers;
//use \GKTOMK\Lib\Controller;

//use \GKTOMK\Views\IndexView;
use GKTOMK\Models\AddclassModel;
use GKTOMK\Models\EventsMoyklass;
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

        if (empty($_SESSION['password']) or !in_array($_SESSION['password'], CONFIG['admin_password'])) {
            die('Access error! ' . $_REQUEST['password']);
        }


        $this->View = new \GKTOMK\Views\IndexView();

        $this->View->setVar('PASSWORD', $_REQUEST['password']);
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


            $np2 = 0;
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


                /*
                                if (isset($expName[1])) {
                                    // echo (int)$expName[0];

                                    $cnt = 0;
                                    if(isset($data[$cours['name']][(int)$expName[0]][(int)$expName[1]])){
                                        $cnt = count($data[$cours['name']][(int)$expName[0]][(int)$expName[1]]);
                                    }
                                    $cnt++;


                                    $data[$cours['name']][(int)$expName[0]][(int)$expName[1]][$cnt] = $val;
                                    ksort($data[$cours['name']][(int)$expName[0]]);
                                    ksort($data[$cours['name']][(int)$expName[0]][(int)$expName[1]]);
                                    ksort($data[$cours['name']][(int)$expName[0]][(int)$expName[1]][$cnt]);
                                    $np2++;
                                }*/


                if (isset($expName[2])) {


                    $data[$cours['name']][(int)$expName[0]][(int)$expName[1]][(int)$expName[2]][] = $val;


                    ksort($data[$cours['name']][(int)$expName[0]]);
                    ksort($data[$cours['name']][(int)$expName[0]][(int)$expName[1]]);
                    ksort($data[$cours['name']][(int)$expName[0]][(int)$expName[1]][(int)$expName[2]]);
                } elseif (isset($expName[1])) {
                    // echo (int)$expName[0];
                    $data[$cours['name']][(int)$expName[0]][(int)$expName[1]][][] = $val;
                    ksort($data[$cours['name']][(int)$expName[0]]);
                    $np2++;
                }


            }

            /// Делаем сортировку есть в программе есть группы
            if (isset($data[$cours['name']]))
                ksort($data[$cours['name']]);

        }


        $POSITION = [];
        // Разбираем массив и формируем список групп
        // print_r($data);

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


            $p1num = 0;
            foreach ($p1 as $p1key => $p1value) {

                if ($p1key == 'position')
                    continue;


                /// Разделитель по классам
                if ($p1num > 0) {
                    echo '<tr><td></td></tr>';
                    echo '<tr><td></td></tr>';
                    echo '<tr><td></td></tr>';
                }

                echo '<tr>';


                for ($i = 1; $i <= 7; $i++) {


                    if (isset($p1value[$i])) {

                        //print_r($p1value[$i]);

                        $td = "";
                        $num = 0;
                        foreach ($p1value[$i] as $p3key => $p3value) {

                            foreach ($p3value as $p4value) {
                                $statText = '';
                                $numStat = 0;
                                $statTime = '';

                                if (isset($p4value['stats']) and !empty($p4value['stats'])) {
                                    foreach ($p4value['stats'] as $stat) {

                                        $numStat++;
                                        if (isset($stat['num_records'])) {


                                            if ($stat['num_records'] >= 0 && $stat['num_records'] < 3)
                                                $color = 'red';
                                            else if ($stat['num_records'] == 3)
                                                $color = 'orange';
                                            else if ($stat['num_records'] == 4 or $stat['num_records'] == 5)
                                                $color = '';
                                            else if ($stat['num_records'] == 6 or $stat['num_records'] == 7)
                                                $color = 'blue';
                                            else if ($stat['num_records'] >= 8)
                                                $color = 'green';


                                            $statText .= " <span style=\"color: $color\">" . $stat['num_records'] . "</span>";
                                        }

                                        if (isset($stat['num_visits'])) {

                                            if ($stat['num_visits'] >= 0 && $stat['num_visits'] < 3)
                                                $color = 'red';
                                            else if ($stat['num_visits'] == 3)
                                                $color = 'orange';
                                            else if ($stat['num_visits'] == 4 or $stat['num_visits'] == 5)
                                                $color = '';
                                            else if ($stat['num_visits'] == 6 or $stat['num_visits'] == 7)
                                                $color = 'blue';
                                            else if ($stat['num_visits'] >= 8)
                                                $color = 'green';

                                            $statText .= "(<span style=\"color: $color\">" . $stat['num_visits'] . "</span>)";
                                        }

                                        if (isset($stat['begin_time'])) {
                                            $statTime = $stat['begin_time'];
                                        }


                                    }
                                    $statTime = substr($statTime, 0, -3);
                                }


                                $text = "<a href='#' onclick='addclass.add.send(\"{$p4value['id']}\");'><span><b>{$p4value['name']}</b> {$statTime}</span><br/><span>{$statText}</span></a>";

                                if ($num > 0) {
                                    $td .= "<hr style='margin-top: 0.4rem; margin-bottom: 0.4rem;'/>";
                                }
                                $td .= "$text";

                                $num++;
                            }

                        }


                        echo "<td style=\"vertical-align: top;\" >$td</td>";
                    } else {
                        echo "<td></td>";
                    }


                }


                echo '</tr>';


                $p1num++;
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
        //var_dump($_REQUEST);


        $addclass = $_POST['addclass'];

        if (empty($addclass['classId'])) {
            $this->answerAjax(['status' => 'error', 'data' => 'empty classId']);
        }

        if (empty($addclass['userEmail'])) {
            $this->answerAjax(['status' => 'error', 'data' => 'empty userEmail']);
        }


        $LeadsModel = new LeadsModel();
        $mk_user = $LeadsModel->getFindUserByEmail($addclass['userEmail']);

        $lead = $LeadsModel->getUserById($addclass['idlead']);

        if (empty($mk_user) or empty($mk_user['id'])) {
            $this->answerAjax(['status' => 'error', 'data' => 'user not found']);
        }

        $Addclass = new AddclassModel();
        $results = $Addclass->addClass($addclass['idlead'], $addclass['classId']);

        $this->answerAjax(['results' => @$results]);

    }

    public function getTest()
    {

        // $Addclass = new AddclassModel();
        //$res = $Addclass->addClass(27, 118283);


        $res = new EventsMoyklass(['object' => ['lessonId' => '7450211']]);


        $res->lesson_start_hours();


        /*$LeadsModel = new LeadsModel();
        $lead = $LeadsModel->getUserById(27);

        $Sync = new SyncModel();
        $sync = $Sync->getSync(['gk_offer' => $lead['gk_offers']]);

        if($sync['demo']==1){ // Пробное занятие

        }

       // print_r($sync);


        $mk_user = $LeadsModel->getFindUserByEmail('anekrasov123@mail.ru');

        print_r($mk_user);


        $statusId = 31034; // Статус "Записан"

        */

        //$result = MoyklassModel::setJoins(['userId' => $mk_user['id'], 'classId' => intval('140461'), 'statusId' => $statusId, 'autoJoin' => false]);

        //print_r($result);
    }

    public function getTesting()
    {

        /*$user = MoyklassModel::getUserById(['userId' => 883119]);

       // $res->startCountUserSubscriptions();

        print_r($user);

        $userMk = MoyklassModel::getUserByEmail($user['email']);*/


        $gk = new GetcourseController();
        $res = $res = $gk->getUpdateUserByIdUserMk(765892); // 765892

        var_dump($res);
    }


}