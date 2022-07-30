<?php


namespace GKTOMK\Models;

use DateTime;
use GKTOMK\Models\MemberModel;

/**
 * Модель для системы отмены записи учеников на занятия
 * */
class CancelLessonModel
{

    public function addCancel($memberId, $mkUid, $mkLessonId)
    {
        # Запрашиваем данные об уроке
        $lesson = MoyklassModel::getLessonById($mkLessonId, ['includeRecords' => 'true']);


        # Сохраняем ID записи на занятие для дальнейших манипуляций
        $record_id = 0;
        if ($lesson['records']) {
            foreach ($lesson['records'] as $record) {
                if ($record['userId'] == $mkUid) {
                    $record_id = $record['id'];
                }
            }
        }

        # Запрашиваем данные по классу и курсу
        $class = MoyklassModel::getClassById($lesson['classId']);
        $course = MoyklassModel::getCourseById($class['courseId']);


        # Считаем какой тип отмены будет присвоен
        $type = 3;
        $lesson_timestart = strtotime($lesson['date'] . ' ' . $lesson['beginTime']);
        $timenow = time();
        $hour_8 = 60 * 60 * 8;

        if (($timenow + $hour_8) <= $lesson_timestart) { // До начала занятия больше 8 часов или ровно 8 часов
            $type = 2; // платная более чем за 8 часов

            if ($this->getFreeCancelCountByDate($memberId, strtotime($lesson['date']), $class['id']) > 0) { // Лимит бесплатных отмен не исчерпан, отмена будет бесплатной
                $type = 1; // бесплатная в рамках лимита
            }
        } else if (($timenow + $hour_8) > $lesson_timestart) { // До начала занятия меньше 8 часов
            $type = 3; // платная менее чем за 8 часов
        }



        // Открываем домашнее задание
        $member = new MemberModel();
        $userIdMK = $member->getMemberParamMkUid($memberId);

        // Добавляем задачу на открытие домашнего задания пользователю
        $HomeworkModel = new HomeworkModel();
        $HomeworkModel->createHwk([
            'id' => $record_id,
            'userId' => $userIdMK,
            'lessonId' => $lesson['id'],
            'visit' => 0,
        ]);


        # Создаем запись в таблицу отмен
        return $this->createCancel([
            'member_id' => $memberId,
            'lesson_id' => $lesson['id'],
            'lesson_date' => $lesson['date'],
            'lesson_beginTime' => $lesson['beginTime'],
            'lesson_endTime' => $lesson['endTime'],
            'lesson_classId' => $lesson['classId'],
            'lesson_topic' => $lesson['topic'],
            'lesson_url' => $lesson['url'],
            'record_id' => $record_id,
            'class_name' => $class['name'],
            'course_name' => $course['name'],

            'date_create' => time(),
            'date_update' => time(),
            'type' => $type,
            'status' => 'new',
            'status_adm' => 'new',
            'comment' => '',
        ]);



    }

    /**
     * Считает сколько осталось бесплатных отмен для клиента
     * */
    public function countCancelLimit($memberId, $date, $class_id=0)
    {
/*
        // Делаем выборку занятий за последние 28 дней
        $month_now_first_day = date('Y-m-d', (time() - (60 * 60 * 24 * 28)));
        $month_now_last_day = date('Y-m-d');

        $lessons = MoyklassModel::getLessons([
            'userId' => $mkUid,
            'date[0]' => $month_now_first_day,
            'date[1]' => $month_now_last_day
        ]);

        $countLessonsForLastMonth = count($lessons['lessons']);

        // Делаем выборку всех занятий с бесплатными отменами
        $lastcancel = $this->getCancelTypeFreeForLastMonth($memberId);

        $countCancelFreeLessonForLastMonth = count(DB::exportAll($lastcancel));

        // Остаток на лимите бесплатных отмен
        $countFreeLimitCancel = $this->getCountFreeCancelByFormula(
            $countLessonsForLastMonth,
            $countCancelFreeLessonForLastMonth,
            4
        );

        return $countFreeLimitCancel;*/

        return $this->getFreeCancelCountByDate($memberId, $date, $class_id);


    }

    private function getCancelTypeFreeForLastMonth($memberId)
    {
        return DB::find('cancellesson', 'type = 0 && status = "done" && member_id = ? ORDER by `lesson_date` ASC', [$memberId]);
    }

    /**
     * Выдает бесплатные отмены за 1 календарный месяц
     * */
    private function getCancelTypeFreeForMonth($memberId, $dateMonth)
    {
        $d = new DateTime( date("Y-m-d", $dateMonth) );
        $month_first_day = $d->format( 'Y-m-1' );
        $month_last_day = $d->format( 'Y-m-t' );
        //echo $month_first_day = strtotime($month_first_day);
        //$month_last_day = strtotime($month_last_day);
        return DB::exportAll(DB::find('cancellesson', 'type = 1 && status = "done" && member_id = :member_id && (lesson_date >= :first_date && lesson_date <= :last_date) ORDER by `lesson_date` ASC',
            [
                'member_id' => $memberId,
                'first_date' => $month_first_day,
                'last_date' => $month_last_day
            ]));
    }
    /**
     * Выдает все отмены за 1 календарный месяц
     * */
    private function getCancelTypeAllForMonth($memberId, $dateMonth)
    {
        $d = new DateTime( date("Y-m-d", $dateMonth) );
        $month_first_day = $d->format( 'Y-m-1' );
        $month_last_day = $d->format( 'Y-m-t' );
        //echo $month_first_day = strtotime($month_first_day);
        //$month_last_day = strtotime($month_last_day);
        return DB::exportAll(DB::find('cancellesson', 'status = "done" && member_id = :member_id && (lesson_date >= :first_date && lesson_date <= :last_date) ORDER by `lesson_date` ASC',
            [
                'member_id' => $memberId,
                'first_date' => $month_first_day,
                'last_date' => $month_last_day
            ]));
    }


    private function getCountFreeCancelByFormula($cntLessons, $cntCancelsFree, $cntFreeCancel = 4)
    {
        $result = floor(($cntLessons / $cntFreeCancel) - $cntCancelsFree);
        if ($result < 0) $result = 0;
        return $result;
    }

    /**
     * Новая формула рассчета бесплатных отмен
     * */
    public function getFreeCancelCountByDate($memberId, $date, $classId = 0)
    {
        $member = new MemberModel();
        $userId = $member->getMemberParamMkUid($memberId);

        $d = new DateTime( date("Y-m-d", $date) );
        $month_now_first_day = $d->format( 'Y-m-1' );
        $month_now_last_day = $d->format( 'Y-m-t' );

        $free_cancel = $this->getCancelTypeFreeForMonth($memberId, $date);
        $free_cancel_cnt = count($free_cancel);
        $all_cancel = $this->getCancelTypeAllForMonth($memberId, $date);
        $all_cancel_cnt = count($all_cancel);


        //var_dump($free_cancel);


        $lessons = MoyklassModel::getLessons([
            'userId' => $userId,
            'date[0]' => $month_now_first_day,
            'date[1]' => $month_now_last_day
            ]
        );

        //var_dump($lessons);

        $GroupsModel = new GroupsModel();
        $individualGroups = $GroupsModel->getIndividualGroups();
        $individualGroupIds = [];
        foreach ($individualGroups as $individualGroup) {
            $individualGroupIds[] = $individualGroup['group_id_mk'];
        }

        $individualActive = false;
        if($classId>0 && in_array($classId, $individualGroupIds)){
            $individualActive = true;
        }

        //print_r($lessons);
        $count_lessons = 0;
        foreach ($lessons['lessons'] as $lesson) {
            //print_r($lesson);
            if($lesson['free'] || $lesson['test'])
                continue;

            // Если считаем бесплатные отмены только для индивидуальных занятий
            if($individualActive==true && !in_array($lesson['classId'], $individualGroupIds))
                continue;

            // Если считаем бесплатные отмены только для групповых занятий
            if($individualActive==false && in_array($lesson['classId'], $individualGroupIds))
                continue;

            $count_lessons++;
        }

        //echo $count_lessons;
        //echo $cnt;

        $count_lessons = $count_lessons + $all_cancel_cnt;
        $cnt = $count_lessons / 4;
        $cnt = $cnt - $free_cancel_cnt;
        //echo $cnt;


        if($cnt < 1 and $cnt > 0){
            $cnt_res = 1;
        }else if($cnt > 1 or $cnt == 1)
            $cnt_res = floor($cnt);
        else
            $cnt_res = 0;

        $result = [
            'count_result' => $cnt_res,
            'count_lessons' => $count_lessons,
            'count_free_cancel' => $free_cancel_cnt
        ];

        //print_r($result);

        return $cnt_res;

    }

    /*
     * Выдвет значения формулы
     * */
    public function getDataByFormula(){

    }

    public function createCancel($data = [])
    {
        $lessoncancel = DB::dispense('cancellesson');
        foreach ($data as $key => $value) {
            $lessoncancel->$key = $value;
        }
        return DB::store($lessoncancel);
    }

    /**
     * Выводит отмены, где занятия еще не начались
     * */
    public function getCancelLessonsHaveNotStarted($memberId)
    {
        return DB::exportAll(DB::find('cancellesson', 'member_id = ? ORDER by `lesson_date` ASC', [$memberId]));

    }

    /**
     * Выводит отмены, где занятия еще не начались
     * */
    public function getCancelLessonsHaveNotStarted2($memberId)
    {
        return DB::getAssoc('SELECT 
			*,
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
			`l`.`lesson_id_mk`', ['member_id' => $memberId]);

    }

    public function setCancel($data = [])
    {
        $cancel = DB::load('cancellesson', $data['id']);
        foreach ($data as $key => $value) {
            if ($key == 'id')
                continue;
            $cancel->{$key} = $value;
        }
        return DB::store($cancel);
    }

    public function sendCancel($cancelId)
    {

        $getCancel = $this->getCancel('id', $cancelId);
        $getCancel = DB::exportAll($getCancel)[0];

        $this->setStatus($cancelId, 'in_progress');

        //var_dump($getCancel);
        switch ($getCancel['type']) {

            case 1: // Удаляем заявку
                $record = MoyklassModel::deleteLessonRecord($getCancel['record_id']);
                break;
            case 2: // Отменяем заявку и ставим статус "Б"
            case 3:
                $record = MoyklassModel::setLessonRecord($getCancel['record_id'], [
                    'visit' => false,
                    'goodReason' => true,
                ]);
                break;
        }

        if (isset($record['code']))
            $status = 'error';
        else
            $status = 'done';

        $this->setStatus($cancelId, $status, $record);


        return $getCancel;
    }

    private function getCancel($key, $value)
    {
        return DB::find('cancellesson', '`' . $key . '`=:' . $key, ['' . $key . '' => $value]);
    }

    /*
     * Метод отправляет отмену в мойклсс
     * */

    public function setStatus($cancelId, $status = 'new', $result = [])
    {
        $cancel = DB::load('cancellesson', $cancelId);
        $cancel->status = $status;
        $cancel->date_update = time();
        $cancel->log = json_encode($result);
        DB::store($cancel);
    }

    /**
     * Вывод списка отмен для шаблона
     * */
    public function buildLogs()
    {
        $Member = new MemberModel();
        $cancels = DB::find('cancellesson', 'ORDER by `id` DESC');
        $cancels = DB::exportAll($cancels);

        $result = [];
        foreach ($cancels as $cancel) {
            $cancel['member'] = $Member->getMemberData($cancel['member_id']);
            $result[] = $cancel;
        }
        //var_dump($result);

        return $result;
    }



}
