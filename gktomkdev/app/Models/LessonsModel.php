<?php


namespace GKTOMK\Models;


class LessonsModel
{
    public function __construct()
    {
        DB::init();
    }

    public function editLesson($data)
    {
        if(empty($data['id']))
           return;

        $get = $this->getLessonByLessonIdMK($data['id']);

        if (empty($get['id'])) {
            $lesson = DB::dispense('lessons');
        } else {
            $lesson = DB::load('lessons', $get['id']);
        }

        $data['CLASS'] = MoyklassModel::getClassById($data['classId']);
        $data['COURSE'] = MoyklassModel::getCourseById($data['CLASS']['courseId']);

        $lesson->lesson_id_mk = $data['id'];
        $lesson->class_id_mk = $data['classId'];
        $lesson->course_id_mk = $data['COURSE']['id'];
        $lesson->class_name = $data['CLASS']['name'];
        $lesson->course_name = $data['COURSE']['name'];
        $lesson->timestart = strtotime($data['date'] . ' ' . $data['beginTime']);
        $lesson->beginTime = $data['beginTime'];
        $lesson->endTime = $data['endTime'];
        $lesson->date = $data['date'];
        $lesson->description = $data['description'];
        $lesson->topic = $data['topic'];
        $lesson->status = $data['status'];
        $lesson->room_id_mk = $data['roomId'];

        if(!empty($data['records'])){
            $this->deleteRecordsLessonByLessonId($data['id']);
            foreach ($data['records'] as $record) {
                $this->editRecordLesson($record);
            }
        }

        //print_r($data['teacherIds']);
        if(!empty($data['teacherIds'])){
            foreach ($data['teacherIds'] as $teacherId) {
                $this->editTeachersLesson($teacherId, $data['id']);
            }
        }

        return DB::store($lesson);


    }

    public function deleteLessonByLessonId($lesson_id){
        DB::exec('DELETE FROM `lessons` WHERE `lesson_id_mk`=?', [$lesson_id]);
        $this->deleteRecordsLessonByLessonId($lesson_id);
    }

    public function setDataLesson($data = []){
        $lesson = DB::load('lessons', $data['id']);
        unset($data['id']);
        foreach ($data as $key => $value) {
            $lesson->{$key} = $value;
        }
        DB::store($lesson);
    }


    public function editTeachersLesson($teacher_id_mk, $lesson_id_mk)
    {
        $get = $this->getTeacherLesson($teacher_id_mk, $lesson_id_mk);

        //var_dump($get);
        if (empty($get['id'])) {
            $record = DB::dispense('teacherslesson');

            $record->teacher_id_mk = $teacher_id_mk;
            $record->lesson_id_mk = $lesson_id_mk;
            DB::store($record);
        }

    }

    public function getTeacherLesson($teacher_id_mk, $lesson_id_mk)
    {
        return DB::getRow('SELECT * FROM `teacherslesson` WHERE `teacher_id_mk`=? && `lesson_id_mk`=?', [$teacher_id_mk, $lesson_id_mk]);
    }

    public function getTeachersByLessonId($lesson_id_mk)
    {
        return DB::getRow('SELECT * FROM `teacherslesson` WHERE `lesson_id_mk`=?', [$lesson_id_mk]);

    }

    public function editRecordLesson($data = [])
    {
        $get = $this->getRecordLessonByRecordLessonIdMK($data['id']);

        if (empty($get['id'])) {
            $record = DB::dispense('recordslesson');
        } else {
            $record = DB::load('recordslesson', $get['id']);
        }

        $record->record_id_mk = $data['id'];
        $record->lesson_id_mk = $data['lessonId'];
        $record->user_id_mk = $data['userId'];
        $record->free = $data['free'];
        $record->test = $data['test'];
        $record->skip = $data['skip'];
        $record->visit = $data['visit'];
        $record->paid = $data['paid'];
        $record->goodReason = $data['goodReason'];
        DB::store($record);

    }

    public function deleteRecordsLessonByLessonId($lesson_id){
        DB::exec('DELETE FROM `recordslesson` WHERE `lesson_id_mk`=?', [$lesson_id]);
    }

    public function deleteRecordLessonByLessonIdAndUserId($lesson_id, $user_id){
        return DB::exec('DELETE FROM `recordslesson` WHERE `lesson_id_mk`=:lesson_id && `user_id_mk`=:user_id', ['lesson_id' => $lesson_id, 'user_id' => $user_id]);
    }


    public function getRecordLessonByRecordLessonIdMK($record_id_mk)
    {
        return DB::getRow('SELECT * FROM `recordslesson` WHERE `record_id_mk`=?', [$record_id_mk]);
    }

    public function getLessonByLessonIdMK($lesson_id_mk)
    {
        return DB::getRow('SELECT * FROM `lessons` WHERE `lesson_id_mk`=?', [$lesson_id_mk]);
    }

    public function addLesson($data = [])
    {

        $data['CLASS'] = MoyklassModel::getClassById($data['classId']);
        $data['COURSE'] = MoyklassModel::getCourseById($data['CLASS']['courseId']);

        $lesson = DB::dispense('lessons');
        $lesson->lesson_id_mk = $data['id'];
        $lesson->class_id_mk = $data['classId'];
        $lesson->course_id_mk = $data['COURSE']['id'];
        $lesson->class_name = $data['CLASS']['name'];
        $lesson->course_name = $data['COURSE']['name'];
        $lesson->timestart = strtotime($data['date'] . ' ' . $data['beginTime']);
        $lesson->beginTime = $data['beginTime'];
        $lesson->endTime = $data['endTime'];
        $lesson->date = $data['date'];
        $lesson->description = $data['description'];
        $lesson->topic = $data['topic'];
        $lesson->status = $data['status'];
        $lesson->room_id_mk = $data['roomId'];

        if(!empty($data['records'])){
            foreach ($data['records'] as $record) {
                $this->addRecordLesson($record);
            }
        }

        if(!empty($data['teacherIds'])){
            foreach ($data['teacherIds'] as $teacherId) {
                $this->addTeacherLesson($teacherId, $lesson['id']);
            }
        }


        return DB::store($lesson);

    }


    public function addRecordLesson($data = [])
    {
        $record = DB::dispense('recordslesson');
        $record->record_id_mk = $data['id'];
        $record->lesson_id_mk = $data['lessonId'];
        $record->user_id_mk = $data['userId'];
        $record->free = $data['free'];
        $record->test = $data['test'];
        $record->skip = $data['skip'];
        $record->visit = $data['visit'];
        $record->paid = $data['paid'];
        $record->goodReason = $data['goodReason'];
        DB::store($record);

    }

    public function getLessonsByUserIdMk($user_id, $memberId)
    {


        //echo 'SELECT * FROM `recordslesson` `rl`,`lessons` `l` WHERE `rl`.`user_id_mk`=? && `l`.`lesson_id_mk`=`rl`.`lesson_id_mk`';
        $lessons = DB::getAll('SELECT *,`rl`.`id` `id`, `rl`.`lesson_id_mk` `lesson_id_mk`
                                FROM 
                                `recordslesson` `rl` 
                                left join `lessons` `l` on `l`.`lesson_id_mk` = `rl`.`lesson_id_mk`
                                left join `videorecords` `vr` on `vr`.`lesson_id_mk` = `rl`.`lesson_id_mk`
                                left join `teacherslesson` `tl` on `tl`.`lesson_id_mk` = `rl`.`lesson_id_mk`
                                WHERE 
                                `rl`.`user_id_mk`=:user_id
                                GROUP BY `l`.`lesson_id_mk`
                                ORDER by `timestart` DESC ', ['user_id' => $user_id]);
        /*$lessons = DB::getAll('(
	SELECT DESTINCT
		`l`.*, 
		"0" `cancel`,
		`rl`.`id` `id`, 
		`rl`.`lesson_id_mk` `lesson_id_mk` ,
        `tl`.`teacher_id_mk` `teacher_id_mk`
	FROM 
		`recordslesson` `rl` 
		left join `lessons` `l` on `l`.`lesson_id_mk` = `rl`.`lesson_id_mk` 
		left join `videorecords` `vr` on `vr`.`lesson_id_mk` = `rl`.`lesson_id_mk` 
		left join `teacherslesson` `tl` on `tl`.`lesson_id_mk` = `rl`.`lesson_id_mk` 
	WHERE 
		`rl`.`user_id_mk` = :user_id 
	GROUP BY 
		`l`.`lesson_id_mk`
) 
UNION 
	(
		SELECT 
			`l`.*, 
			`cl`.`type` `cancel`,
			`cl`.`id` `id`, 
			`cl`.`lesson_id` `lesson_id_mk`,
            `tl`.`teacher_id_mk` `teacher_id_mk`
		FROM 
			`cancellesson` `cl` 
			left join `lessons` `l` on `l`.`lesson_id_mk` = `cl`.`lesson_id` 
			left join `videorecords` `vr` on `vr`.`lesson_id_mk` = `cl`.`lesson_id` 
			left join `teacherslesson` `tl` on `tl`.`lesson_id_mk` = `cl`.`lesson_id` 
		WHERE 
			`cl`.`member_id` = :member_id
		GROUP BY 
			`l`.`lesson_id_mk`
	)
    ORDER BY `timestart` DESC', ['user_id' => $user_id, 'member_id' => $memberId]);*/



        # Запрашиваем отмененные занятия, чтобы встроить их в список
        $CancelLessonModel = new CancelLessonModel();
        $CancelLessonsHaveNotStarted = $CancelLessonModel->getCancelLessonsHaveNotStarted2($memberId);
        $CancelLessonsHaveNotStarted = $this->templateLesson($CancelLessonsHaveNotStarted);
        // Объединяем уроки из моего класса и отмененные уроки
        $lessons = array_merge($lessons, $CancelLessonsHaveNotStarted);

        // Сортируем все уроки по дате и времени
        usort($lessons, "\GKTOMK\Models\LessonsModel::cmp");

        $dataList = [];
        $ind = 0;
        $cancelLessonsUniqueId = []; // Здесь сохраним айдишники отмененных занятий
        foreach ($CancelLessonsHaveNotStarted as $item) {
            $cancelLessonsUniqueId[] = $item['lesson_id_mk'];
        }


        $mnths = array("1" => "января", "2" => "февраля", "3" => "марта", "4" => "апреля", "5" => "мая", "6" => "июня", "7" => "июля", "8" => "августа", "9" => "сентября", "10" => "октября", "11" => "ноября", "12" => "декабря");
        $mnths_nominative = array("1" => "январь", "2" => "февраль", "3" => "март", "4" => "апрель", "5" => "май", "6" => "июнь", "7" => "июль", "8" => "август", "9" => "сентябрь", "10" => "октябрь", "11" => "ноябрь", "12" => "декабрь");
        $weekdays = array("1" => "пн", "2" => "вт", "3" => "ср", "4" => "чт", "5" => "пт", "6" => "сб", "7" => "вс");


        $dataList = [];
        $GroupsModel = new GroupsModel();
        $HomeworklinksModel = new HomeworklinksModel();
        foreach ($lessons as $lesson) {

            // Убираем из списка уроки из моего класса которые еще не отменены, но у нас в системе уже
            // помечены как заявка на отмену
            if (in_array($lesson['lesson_id_mk'], $cancelLessonsUniqueId) and !isset($lesson['cancel']))
                continue;

            if (($lesson['timestart'] + (60 * 60 * 1)) > time()) // Показываем уроки только спустя час после их начала
                continue;

            $lesson['id'] = $lesson['lesson_id_mk'];
            $lesson['daynumber'] = date("d", $lesson['timestart']);
            $lesson['dayofweek'] = date("D", $lesson['timestart']);
            $lesson['year'] = date("Y", $lesson['timestart']);
            if ($lesson['year'] == date("Y"))
                unset($lesson['year']);

            $lesson['monthtxt'] = $mnths[date("n", $lesson['timestart'])];
            $lesson['mnths_nominative'] = $mnths_nominative[date("n", $lesson['timestart'])];
            $lesson['weekday'] = $weekdays[date("N", $lesson['timestart'])];

            $groupsync = $GroupsModel->getGroupsyncByGroupIdMK($lesson['class_id_mk']);
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

    public function getLastLessonByUserIdMK($user_id_mk)
    {
        return DB::getRow('SELECT * FROM `recordslesson` `rl`,`lessons` `l` WHERE `rl`.`user_id_mk`=:user_id && `l`.`lesson_id_mk`=`rl`.`lesson_id_mk` ORDER BY `timestart` DESC LIMIT 1', ['user_id' => $user_id_mk]);
    }

    public function loadLessonsUserByUserIdMK($user_id_mk)
    {
        //$get = $this->getLastLessonByUserIdMK($user_id_mk);


        $query_mk = [
            'userId' => $user_id_mk,
            'includeRecords' => 'true'
        ];

        $member = new MemberModel();
        $member_data = $member->getMemberByMkUid($user_id_mk);

        if(empty($member_data['historyload']) or empty($member_data['historyfirstload'])){ // Если история пуста, то берем выборку от начала времени
            $query_mk['date[0]'] = date('Y-m-d', 1);
            $query_mk['date[1]'] = date('Y-m-d', (time()+60*60*24*60));
            $member->setUpdateMember(['id' => $member_data['id'], 'historyfirstload' => 1]);
            $member->setUpdateMember(['id' => $member_data['id'], 'historyload' => time()]);
        }else if(($member_data['historyload']+60*60*24*59) <= time()){
            $query_mk['date[0]'] = date('Y-m-d', $member_data['historyload']);
            $query_mk['date[1]'] = date('Y-m-d', (time()+60*60*24*60));
        }else
            return;

        $lessons = MoyklassModel::getLessons($query_mk);

        //print_r($query_mk);
        //print_r($lessons);

        foreach ($lessons['lessons'] as $lesson) {
            $this->editLesson($lesson);
        }
    }

    public function setSynchronizationByDate($date_start, $date_end){
        //$lessons =  DB::getAll('SELECT * FROM `lessons` WHERE `date`="2021-12-13" or `date`="2021-12-12"');
       /* $videorecords = new VideorecordsModel();
        foreach ($lessons as $lesson) {
            echo $videorecords->editRecord([
                'lesson_id_mk' => $lesson['lesson_id_mk'],
                'timeend' => strtotime($lesson['date'] .' ' . $lesson['end_time']),
                'status' => 'new',
            ]);
        }*/

        $query_mk = [
            'includeRecords' => 'true'
        ];
        $query_mk['date[0]'] = date("Y-m-d", $date_start);
        $query_mk['date[1]'] = date("Y-m-d", $date_end);
        print_r($query_mk);
        $lessons = MoyklassModel::getLessons($query_mk);
        //$lessons_my = $this->getLessonsByDate(date("Y.m.d", $date_start), date("Y.m.d", $date_end));

        $this->deleteLessonsByDate(date("Y.m.d", $date_start), date("Y.m.d", $date_end));
        foreach ($lessons['lessons'] as $lesson) {
            $this->editLesson($lesson);
        }
        print_r($lessons);
    }

    private function getLessonsByDate($date_start, $date_end){
        return DB::getAll('SELECT * FROM `lessons` WHERE `date`>=:date_start && `date`<=:date_end', ['date_start' => $date_start, 'date_end' => $date_end]);
    }

    private function deleteLessonsByDate($date_start, $date_end){
        return DB::exec('DELETE FROM `lessons` WHERE `date`>=:date_start && `date`<=:date_end', ['date_start' => $date_start, 'date_end' => $date_end]);
    }

    /// SELECT `l`.`id`, MAX(`l`.`lesson_id_mk`), `l`.`class_id_mk`, `timestart`, MAX(`l`.`timestart`) FROM `lessons` `l` INNER JOIN (SELECT `lesson_id_mk` FROM `recordslesson` WHERE `user_id_mk`=905158 ORDER BY `id` DESC) `rl` WHERE `l`.`status`=1 && `l`.`lesson_id_mk`=`rl`.`lesson_id_mk` GROUP by `l`.`class_id_mk`
    // SELECT `l`.`id`,`rl`.`id` `rlid`, `l`.`class_id_mk`,`l`.`timestart` FROM `recordslesson` `rl`
    //                            LEFT JOIN `lessons` `l` ON `rl`.`lesson_id_mk`=`l`.`lesson_id_mk`
    //                            WHERE `user_id_mk`=:user_id_mk && `l`.`status`=1
    //                            GROUP BY `l`.`class_id_mk`
    //                            ORDER BY  `l`.`timestart` DESC
    public function getLessonsByUserIdMKAndTime($user_id_mk, $timestart=0){
        return DB::getAll('SELECT `l`.`id`, `l`.`class_id_mk`, `timestart` FROM `lessons` `l` INNER JOIN `recordslesson` `rl` ON `rl`.`lesson_id_mk`=`l`.`lesson_id_mk` AND `rl`.`user_id_mk`=:user_id_mk
            ', ['user_id_mk' => $user_id_mk]);
    }

    /*
    * Сортировка массива по дате, затем по времени
    * */

    private function templateLesson($data = [])
    {
        $newData = [];
        $i = 0;
        foreach ($data as $dat) {
            $newData[$i] = $dat;
            $newData[$i]['lesson_id_mk'] = $dat['lesson_id'];
            $newData[$i]['class_id_mk'] = $dat['lesson_class_id'];
            $newData[$i]['class_name'] = $dat['class_name'];
            $newData[$i]['course_name'] = $dat['course_name'];
            $newData[$i]['date'] = $dat['lesson_date'];
            $newData[$i]['timestart'] = strtotime($dat['lesson_date'].' '.$dat['lesson_begin_time']);
            $newData[$i]['begin_time'] = $dat['lesson_begin_time'];
            $newData[$i]['end_time'] = $dat['lesson_end_time'];
            $newData[$i]['topic'] = $dat['lesson_topic'];
            $newData[$i]['cancel'] = $dat['type'];
            $newData[$i]['cancel_status'] = $dat['status_adm'];
            $i++;
        }
        return $newData;
    }

    function cmp($b, $a)
    {
        // extract year, month and day from date
        list($a_month, $a_day, $a_year) = explode('/', $a['date']);
        list($b_month, $b_day, $b_year) = explode('/', $b['date']);
        // compare the correctly ordered strings
        return strcmp($a_year . $a_month . $a_day . $a['beginTime'] . $a['endTime'],
            $b_year . $b_month . $b_day . $b['beginTime'] . $b['endTime']);
    }

}