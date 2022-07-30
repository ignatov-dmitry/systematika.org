<?php


namespace GKTOMK\Models;


class ZoomaccountsModel
{
    public function __construct()
    {
        DB::init();
    }

    public function editAccount($data = [])
    {
        if(empty($data['id'])){
            $zoomaccount = DB::dispense('zoomaccounts');
        }else{
            $zoomaccount = DB::load('zoomaccounts', $data['id']);
            unset($data['id']);
        }
        foreach ($data as $key => $value) {
            $zoomaccount->{$key} = $value;
        }
        DB::store($zoomaccount);
    }

    public function delAccount($id)
    {
        DB::trashBatch('zoomaccounts', [$id]);
    }

    public function getAllAccounts(){
        return DB::getAll('SELECT * FROM `zoomaccounts`');
    }

    public function getAccountDataById($accountId)
    {
        return DB::getRow('SELECT * FROM `zoomaccounts` WHERE `id`=? LIMIT 1', [$accountId]);
    }

    public function getAccountIdByGroupIdMK($group_id_mk)
    {
        $GroupsModel = new GroupsModel();
        $getDataGroup = $GroupsModel->getGroupsyncByGroupIdMK($group_id_mk);

        //var_dump($getDataGroup);

        return $getDataGroup['zoomaccount_id'];
    }

    public function getAccountIdByLessonIdMK($lesson_id_mk)
    {
        $LessonModel = new LessonsModel();
        $getDataLesson = $LessonModel->getLessonByLessonIdMK($lesson_id_mk);

        //var_dump($getDataLesson);

        return $this->getAccountIdByGroupIdMK($getDataLesson['class_id_mk']);
    }

}