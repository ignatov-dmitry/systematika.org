<?php


namespace GKTOMK\Models;


class ScheduleModel
{

    /*
     * Отдает подготовленный список расписания для выдачи в шаблон
     * */
    public function getSchedule($memberId, $mk_uid)
    {

        $month_now_first_day = date('Y-m-d');
        $month_now_last_day = date('Y-m-d', (time() + (60 * 60 * 24 * 14)));


        $mnths = array("1" => "января", "2" => "февраля", "3" => "марта", "4" => "апреля", "5" => "мая", "6" => "июня", "7" => "июля", "8" => "августа", "9" => "сентября", "10" => "октября", "11" => "ноября", "12" => "декабря");
        $mnths_nominative = array("1" => "январь", "2" => "февраль", "3" => "март", "4" => "апрель", "5" => "май", "6" => "июнь", "7" => "июль", "8" => "август", "9" => "сентябрь", "10" => "октябрь", "11" => "ноябрь", "12" => "декабрь");
        $weekdays = array("1" => "пн", "2" => "вт", "3" => "ср", "4" => "чт", "5" => "пт", "6" => "сб", "7" => "вс");

        $lessons = MoyklassModel::getLessons([
            'userId' => $mk_uid,
            'date[0]' => $month_now_first_day,
            'date[1]' => $month_now_last_day
        ]);


        # Запрашиваем отмененные занятия, чтобы встроить их в список
        $CancelLessonModel = new CancelLessonModel();
        $CancelLessonsHaveNotStarted = $CancelLessonModel->getCancelLessonsHaveNotStarted($memberId);
        $CancelLessonsHaveNotStarted = $this->templateLesson($CancelLessonsHaveNotStarted);
        // Объединяем уроки из моего класса и отмененные уроки
        $lessons['lessons'] = array_merge($lessons['lessons'], $CancelLessonsHaveNotStarted);

        // Сортируем все уроки по дате и времени
        usort($lessons['lessons'], "\GKTOMK\Models\ScheduleModel::cmp");

        $dataList = [];
        $ind = 0;
        $cancelLessonsUniqueId = []; // Здесь сохраним айдишники отмененных занятий
        foreach ($CancelLessonsHaveNotStarted as $item) {
            $cancelLessonsUniqueId[] = $item['id'];
        }

        $GroupsModel = new GroupsModel();
        $HomeworklinksModel = new HomeworklinksModel();
        foreach ($lessons['lessons'] as $lesson) {

            // Убираем из списка уроки из моего класса которые еще не отменены, но у нас в системе уже
            // помечены как заявка на отмену
            if (in_array($lesson['id'], $cancelLessonsUniqueId) and !isset($lesson['cancel']))
                continue;

            $ind++;
            $lesson['index'] = $ind;
            $lesson['timestart'] = strtotime($lesson['date'] . ' ' . $lesson['beginTime']);

            if (($lesson['timestart'] + (60 * 60 * 2)) < time()) // Не показываем уроки спустя 2 часа после их начала
                continue;

            $lesson['daynumber'] = date("d", strtotime($lesson['date']));
            $lesson['dayofweek'] = date("D", strtotime($lesson['date']));

            $lesson['monthtxt'] = $mnths[date("n", strtotime($lesson['date']))];
            $lesson['mnths_nominative'] = $mnths_nominative[date("n", $lesson['timestart'])];

            $lesson['weekday'] = $weekdays[date("N", strtotime($lesson['date']))];

            $lesson['CLASS'] = MoyklassModel::getClassById($lesson['classId']);
            $lesson['COURSE'] = MoyklassModel::getCourseById($lesson['CLASS']['courseId']);

            $lesson['url'] = preg_replace('!(https|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?&_/]+!', "\\0", $lesson['topic']);;
            $lesson['topic'] = preg_replace('!(https|ftp|scp)(s)?:\/\/[a-zA-Z0-9.?&_/]+!', "<a href=\"\\0\" class=\"monthly-list-time-start\" target='_blank'>\\0</a>", $lesson['topic']);;


            $groupsync = $GroupsModel->getGroupsyncByGroupIdMK($lesson['classId']);
            //$lesson['groupsync'] = $groupsync;

            $lesson['class'] = $GroupsModel->getClassById($groupsync['class_id'])[0];
            $lesson['program'] = $GroupsModel->getProgramById($groupsync['program_id'])[0];
            $lesson['color'] = $groupsync['color'];

            $homework_group = $HomeworklinksModel->findGroup($lesson['description']);
            $homework_link = $HomeworklinksModel->getWomeworklinkByGroup($homework_group);
            $lesson['homework_link'] = $homework_link['link'];


            $dataList[] = $lesson;
        }



        return $dataList;
    }


    /*
    * Сортировка массива по дате, затем по времени
    * */

    private function templateLesson($data = [])
    {
        $newData = [];
        $i = 0;
        foreach ($data as $dat) {
            $newData[$i]['id'] = $dat['lesson_id'];
            $newData[$i]['date'] = $dat['lesson_date'];
            $newData[$i]['beginTime'] = $dat['lesson_begin_time'];
            $newData[$i]['endTime'] = $dat['lesson_end_time'];
            $newData[$i]['classId'] = $dat['lesson_class_id'];
            $newData[$i]['topic'] = $dat['lesson_topic'];
            $newData[$i]['cancel'] = $dat['type'];
            $newData[$i]['cancel_status'] = $dat['status_adm'];
            $i++;
        }
        return $newData;
    }

    function cmp($a, $b)
    {
        // extract year, month and day from date
        list($a_month, $a_day, $a_year) = explode('/', $a['date']);
        list($b_month, $b_day, $b_year) = explode('/', $b['date']);
        // compare the correctly ordered strings
        return strcmp($a_year . $a_month . $a_day . $a['beginTime'] . $a['endTime'],
            $b_year . $b_month . $b_day . $b['beginTime'] . $b['endTime']);
    }

    public function getScheduleWidget($memberId=0, $mk_uid=0, $classId = 0)
    {


        if($mk_uid==0 and empty($classId))
            return [];
        //echo $mk_uid;

        $month_now_first_day = date('Y-m-d', (time() - (60 * 60 * 24 * 21)));
        $month_now_last_day = date('Y-m-d', (time() + (60 * 60 * 24 * 14)));

        $mnths = array("1" => "января", "2" => "февраля", "3" => "марта", "4" => "апреля", "5" => "мая", "6" => "июня", "7" => "июля", "8" => "августа", "9" => "сентября", "10" => "октября", "11" => "ноября", "12" => "декабря");
        $weekdays = array("1" => "пн", "2" => "вт", "3" => "ср", "4" => "чт", "5" => "пт", "6" => "сб", "7" => "вс");

        $dataMK = [
            'date[0]' => $month_now_first_day,
            'date[1]' => $month_now_last_day,
            'includeRecords' => 'true',
        ];

        /*
         * Если указан пользователь и не указан класс, то делаем выборку по нему
         * */
        if($mk_uid and empty($classId)){
            $dataMK['userId'] = $mk_uid;
        }

        // Если указан класс, то делаем выборку только по нему
        if (!empty($classId)) {
            $dataMK['classId'] = $classId;
        }

        $lessons = MoyklassModel::getLessons($dataMK);
        //print_r($dataMK);


        if(empty($lessons['lessons'])){
            return [];
        }


        # Запрашиваем отмененные занятия, чтобы встроить их в список
        if($memberId > 0){
            $CancelLessonModel = new CancelLessonModel();
            $CancelLessonsHaveNotStarted = $CancelLessonModel->getCancelLessonsHaveNotStarted($memberId);
            $CancelLessonsHaveNotStarted = $this->templateLesson($CancelLessonsHaveNotStarted);
            // Объединяем уроки из моего класса и отмененные уроки
            $lessons['lessons'] = array_merge($lessons['lessons'], $CancelLessonsHaveNotStarted);

            $cancelLessonsUniqueId = []; // Здесь сохраним айдишники отмененных занятий
            foreach ($CancelLessonsHaveNotStarted as $item) {
                if(empty($classId) or $classId == $item['classId'])
                    $cancelLessonsUniqueId[] = $item['id'];
            }
        }


        //print_r($lessons);
        // Сортируем все уроки по дате и времени
        usort($lessons['lessons'], "\GKTOMK\Models\ScheduleModel::cmp");

        $dataList = [];
        $ind = 0;




        foreach ($lessons['lessons'] as $lesson) {

            // Убираем из списка уроки из моего класса которые еще не отменены, но у нас в системе уже
            // помечены как заявка на отмену
            if ($memberId > 0 and (!in_array($lesson['id'], $cancelLessonsUniqueId) and isset($lesson['cancel'])))
                continue;


            $ind++;
            $lesson['index'] = $ind;

            $lesson['records'] = $this->getCalcStatRecords($lesson['records'], $mk_uid, strtotime($lesson['date'] . ' ' .$lesson['beginTime']));
            $lesson['weeknumber'] = date("W", strtotime($lesson['date']));


            $lesson['CLASS'] = MoyklassModel::getClassById($lesson['classId']);
            $lesson['COURSE'] = MoyklassModel::getCourseById($lesson['CLASS']['courseId']);

            $dataList[] = $lesson;
            //print_r($lesson);

        }
        //print
        $dataList = $this->getCalcStatLessons($dataList);


        return $dataList;
    }

    /*
     * Считает количество записей и посещений
     * */
    private function getCalcStatRecords($records = [], $mk_uid, $timelesson)
    {
        //echo $timelesson;

        //print_r($records);

        $visited = 'notrecord';

        if (is_array($records) && count($records) > 0) {
            foreach ($records as $record) {
                //var_dump($record);

                if ($mk_uid == $record['userId']) {

                    $visited = 'recorded';

                    if ($record['visit'] == true)
                        $visited = 'visit';
                    else if ($record['visit'] == false and $timelesson < time()){
                        $visited = 'dontpay';

                    }else if ($record['goodReason'] == 1)
                        $visited = 'goodreason';

                }
            }
        }else
            $records = [];
        // var_dump($visited);
        return ['records' => count($records), 'visited' => $visited];
    }

    /*
     * Считает общую статистику записей и выдает в удобном виде
     * */
    private function getCalcStatLessons($lessons)
    {

        $thisnumweek = date("W");
        $tpl_week = [
            ($thisnumweek - 2) => 'beforelastweek',
            ($thisnumweek - 1) => 'lastweek',
            $thisnumweek => 'thisweek',
            ($thisnumweek + 1) => 'nextweek',
        ];

        // print_r($tpl_week);

        //print_r($lessons);

        $d = [];
        foreach ($lessons as $lesson) {
            if (!array_key_exists($lesson['weeknumber'], $tpl_week)) // Только необходимые месяцы
                continue;

            //print_r($lesson);

            $weektextname = $tpl_week[$lesson['weeknumber']];

            $d[$lesson['CLASS']['id']]['course'] = $lesson['COURSE']['name'];
            $d[$lesson['CLASS']['id']]['class'] = $lesson['CLASS']['name'];
            $d[$lesson['CLASS']['id']]['managerIds'] = $lesson['CLASS']['managerIds'];
            $d[$lesson['CLASS']['id']]['beginTime'] = $lesson['beginTime'];


            // Если была отмена занятия, то делаем новую пометку
            if (isset($lesson['cancel'])){
                if($lesson['cancel_status']=='done' and $lesson['cancel'] == 1)
                    $lesson['records']['visited'] = 'goodreason';
                elseif($lesson['cancel_status']=='done' and $lesson['cancel'] > 1)
                    $lesson['records']['visited'] = 'dontpay';

            }

            $d[$lesson['CLASS']['id']][$weektextname] = [
                'records' => $d[$lesson['CLASS']['id']][$weektextname]['records'] + $lesson['records']['records'],
                'visited' => $lesson['records']['visited'],
                'week' => $weektextname,
            ];
        }


        //print_r($d);
        //echo 'weeknum ' . date("W");

        return $d;
    }




}