<?php


namespace GKTOMK\Models;

use DateTime;
use Exception;
use GKTOMK\Classes\Api\MoyKlass;
use GKTOMK\Models\Systematika\MoyKlass\Lesson;
use GKTOMK\Models\Systematika\MoyKlass\LessonRecord;
class EventsMoyklass extends Events
{

    public function __construct($request)
    {
        parent::__construct($request);
    }

    public function handle()
    {
        if (method_exists($this, $this->request['event'])) {
            return $this->{$this->request['event']}();
        }
        return false;
    }

    /*
     * Общая функция для обновления данных юзера в гк
     * */
    private function updateUserGetcourse()
    {
        $userMk = MoyklassModel::getUserById(['userId' => $this->request['object']['userId']]);
        $GetCourse = new GetcourseModel();
        $GetCourse->updateUserDateVisitByUserIdMK($this->request['object']['userId'])
            ->updateUserSubscriptionsByUserIdMK($this->request['object']['userId'])
            ->setEmail($userMk['email'])
            ->sendUser();
    }


    /*
     * При создании новой записи на занятие - обновляем данные в геткурсе
     * */
    public function lesson_record_new()
    {
        $this->updateUserGetcourse(); // Обновляем данные в гк
        $lesson_id = $this->request['object']['lessonId'];
        $res = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);

        // Сохраняем занятие в историю уроков
        $lessons = new LessonsModel();
        $lessons->editLesson($res);

        // Добавляем пропуски
        if ($this->request['object']['visit'] and $this->request['object']['visit'] == 1) {
            // Добавляем занятие на проверку пропусков. Даелаем отметку в гк, если человек пропустил занятие
            $MissingTrial = new MissingTrialModel();
            $MissingTrial->addMissing($this->request['object']['lessonId']);
        }
    }

    public function lesson_new(){
        $lesson_id = $this->request['object']['lessonId'];
        $res = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);

        // Сохраняем занятие в историю уроков
        $lessons = new LessonsModel();
        $lessons->editLesson($res);
    }

    public function lesson_changed(){
        $lesson_id = $this->request['object']['lessonId'];
        $res = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);

        // Сохраняем занятие в историю уроков
        $lessons = new LessonsModel();
        $lessons->editLesson($res);
    }

    public function lesson_deleted()
    {
        $lesson_id = $this->request['object']['lessonId'];
        // Удаляем урок и записи на него
        $lessons = new LessonsModel();
        $lessons->deleteLessonByLessonId($lesson_id);

        DB::exec('DELETE FROM `mk_lesson_records` WHERE `lessonId`=:lesson_id', ['lesson_id' => $lesson_id]);
        DB::exec('DELETE FROM `mk_lessons` WHERE `id`=:lesson_id', ['lesson_id' => $lesson_id]);
    }

    /*
     * При изменении записи на занятие - обновляем данные в геткурсе
     * */
    public function lesson_record_changed()
    {
        $this->updateUserGetcourse(); // Чисто обновляем данные в гк

        $lesson_id = $this->request['object']['lessonId'];

        $res = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);

        // Сохраняем занятие в историю уроков
        $lessons = new LessonsModel();
        $lessons->editLesson($res);


        // Добавляем пропуски
        if ($this->request['object']['visit'] and $this->request['object']['visit'] == 1) {
            // Добавляем занятие на проверку пропусков. Даелаем отметку в гк, если человек пропустил занятие
            $MissingTrial = new MissingTrialModel();
            $MissingTrial->addMissing($this->request['object']['lessonId']);
        }
    }

    /**
     * @throws Exception
     */
    public function lesson_start()
    {
        $request = $this->request;
        $currentDateTime = new DateTime();
        $lessonDateTime = new DateTime($request['object']['date'] . ' ' . $request['object']['beginTime']);

        $lesson_id = $this->request['object']['lessonId'];
        $res = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);
        $lessons = new LessonsModel();
        $lessons->editLesson($res);

        if (date_diff($currentDateTime, $lessonDateTime)->h <=2)
        {
            $lessonRecords = new LessonRecord();
            (new WhatsappModel())->sendMessages($lessonRecords->getRecordsWithUsers($request['object']['lessonId'], $request['object']['date']), $request);
        }
    }

    /**
     * Принимаем событие "до начала занятие остается менее 6 часов"
     * */
    public function lesson_start_hours()
    {
        $lesson_id = $this->request['object']['lessonId'];
        $res = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);

        // Записываем в задачи на выдачу домашних заданий
        $homework = new HomeworkModel();
        $homework->sendRecords($res['records']);

        // Сохраняем занятие в историю уроков
        $lessons = new LessonsModel();
        $lessons->editLesson($res); // Либо создает запись, либо сохраняет если записи не было

       /* // Сохраняем задачу на сохранение видео-записи
        $videorecords = new VideorecordsModel();
        $videorecords->editRecord([
            'lesson_id_mk' => $res['id'],
            'timeend' => strtotime($res['date'] .' ' . $res['endTime']),
            'status' => 'new',
        ]);*/

    }

    public function lesson_record_deleted(){
        $lessons = new LessonsModel();
        $lesson_id = $this->request['object']['lessonId'];
        $user_id = $this->request['object']['userId'];
        $lessons->deleteRecordLessonByLessonIdAndUserId($lesson_id, $user_id);
        DB::exec('DELETE FROM `mk_lesson_records` WHERE `lessonId`=:lesson_id && `userId`=:user_id', ['lesson_id' => $lesson_id, 'user_id' => $user_id]);
    }


    /**
     * Настройки группы изменены
     * */
    public function class_changed(){
        $Groups = new GroupsModel();
        // Обновляем группу
        $Groups->editGroupsync([
            'group_id_mk' => $this->request['object']['classId'],
            'begin_date' => $_POST['beginDate'],
            'manager_ids' => $_POST['teacherIds'],
        ]);
        // Удаляем неактивные группы
        //$Groups->deleteInactiveGroups();
    }

    public function join_changed()
    {
        $lessonRecords = MoyklassModel::getUserLessonRecords($this->request['object']['userId']);

        foreach ($lessonRecords['lessonRecords'] as $record)
            (new LessonsModel())->editRecordLesson($record);

        GetcourseModel::updateUser($this->request['object']);
    }
}