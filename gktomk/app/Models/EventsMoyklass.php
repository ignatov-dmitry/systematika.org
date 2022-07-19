<?php


namespace GKTOMK\Models;

use \GKTOMK\Models\GetcourseModel;

class EventsMoyklass extends Events
{

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
            return $GetCourse->updateUserDateVisit($userMk['email']);
    }

}