<?php


namespace GKTOMK\Models;

use \GKTOMK\Models\GetcourseModel;

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
    }

    public function lesson_record_changed()
    {
            $userMk = MoyklassModel::getUserById(['userId' => $this->request['object']['userId']]);
            $GetCourse = new GetcourseModel();
            $GetCourse->updateUserDateVisit($userMk['email']);
            $GetCourse->updateUserSubscriptions($userMk['email']);
    }

    /**
     *
     * */
    public function lesson_start_hours()
    {
        $lesson_id = $this->request['object']['lessonId'];
        $res = MoyklassModel::getLessonById($lesson_id, ['includeRecords' => 'true']);

        print_r($res);

        $homework = new HomeworkModel();
        $homework->sendRecords($res['records']);

        // Запускаем обработку выдачи домашних заданий
        $HandlerHwkModel = new HandlerHwkModel();
        $HandlerHwkModel->cronHandle();
    }

}