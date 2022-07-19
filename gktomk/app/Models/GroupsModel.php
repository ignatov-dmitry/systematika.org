<?php


namespace GKTOMK\Models;


class GroupsModel
{

    public function __construct()
    {
        DB::init();
    }

    public function createProgram($data = [])
    {
        $program = DB::dispense('program');
        $program->name = $data['name'];
        $program->shortname = $data['shortname'];
        $program->default = $data['default'];
        $program->show = $data['show'];
        $program->sort = $data['sort'];
        return DB::store($program);
    }

    public function editProgram($data = [])
    {
        $program = DB::load('program', $data['id']);
        unset($data['id']);
        $program->name = $data['name'];
        $program->shortname = $data['shortname'];
        $program->default = $data['default'];
        $program->show = $data['show'];
        $program->sort = $data['sort'];
        return DB::store($program);
    }

    public function deleteProgram($id)
    {
        $program = DB::load('program', $id);
        return DB::trash($program);
    }

    public function getAllPrograms()
    {
        return DB::exportAll(DB::findAll('program', 'ORDER BY `sort` ASC'));
    }

    public function getProgramById($programId)
    {
        //return DB::exportAll(DB::findOne('program', 'WHERE `id`=? ORDER BY `sort` ASC', [$programId]));
        return DB::getAssocRow('SELECT * FROM program WHERE `id`=? ORDER BY `sort` ASC LIMIT 1', [$programId]);
    }

    public function createClass($data = [])
    {
        $program = DB::dispense('class');
        $program->name = $data['name'];
        $program->shortname = $data['shortname'];
        $program->default = $data['default'];
        $program->show = $data['show'];
        $program->sort = $data['sort'];
        return DB::store($program);
    }

    public function editClass($data = [])
    {
        $program = DB::load('class', $data['id']);
        unset($data['id']);
        $program->name = $data['name'];
        $program->shortname = $data['shortname'];
        $program->default = $data['default'];
        $program->show = $data['show'];
        $program->sort = $data['sort'];
        return DB::store($program);
    }

    public function deleteClass($id)
    {
        $class = DB::load('class', $id);
        return DB::trash($class);
    }

    public function getAllClasses()
    {
        return DB::exportAll(DB::findAll('class', 'ORDER BY `sort` ASC'));
        //return DB::getAssoc('SELECT * FROM class ORDER BY `sort` ASC');
    }

    public function getClassById($classId)
    {
        //return DB::exportAll(DB::findOne('class', 'WHERE `id`=? ORDER BY `sort` ASC', [$classId]));
        return DB::getAssocRow('SELECT * FROM class WHERE `id`=? ORDER BY `sort` ASC LIMIT 1', [$classId]);
    }

    public function getAllClassesAssoc()
    {
        //return DB::exportAll(DB::findAll('class', 'ORDER BY `sort` ASC'));
        return DB::getAssoc('SELECT * FROM class ORDER BY `sort` ASC');
    }

    public function getAllGroupsync()
    {
        return DB::exportAll(DB::findAll('groupsync'));
        //return DB::getAssoc('SELECT * FROM class ORDER BY `sort` ASC');
    }

    public function createGroupsync($data = [])
    {
        $groupsync = DB::dispense('groupsync');
        $groupsync->program_id = $data['program_id'];
        $groupsync->class_id = $data['class_id'];
        $groupsync->comment = $data['comment'];
        $groupsync->color = $data['color'];
        $groupsync->show_adm = $data['show_adm'];
        $groupsync->show_user = $data['show_user'];
        $groupsync->begin_date = $data['begin_date'];
        return DB::store($groupsync);
    }

    public function editGroupsync($data = [])
    {
        $get = $this->getGroupsyncByGroupIdMK($data['group_id_mk']);

        if(!empty($get['id'])){
            $groupsync = DB::load('groupsync', $get['id']);
        }else{
            $groupsync = DB::dispense('groupsync');
        }

        foreach ($data as $key => $value) {
            $groupsync->{$key} = $value;
        }

        /*$groupsync->group_id_mk = $data['group_id_mk'];
        $groupsync->program_id = $data['program_id'];
        $groupsync->class_id = $data['class_id'];
        $groupsync->comment = $data['comment'];
        $groupsync->color = $data['color'];
        $groupsync->individual = $data['individual'];
        $groupsync->show_adm = $data['show_adm'];
        $groupsync->show_user = $data['show_user'];
        if($data['begin_date'])
        $groupsync->begin_date = $data['begin_date'];
        if($data['manager_ids'])
            $groupsync->manager_ids = json_encode($data['manager_ids']);*/
        return DB::store($groupsync);
    }

    public function deleteGroupsync($id)
    {
        $program = DB::load('groupsync', $id);
        DB::trash($program);
    }

    public function deleteGroupsyncByClassId($group_id_mk)
    {
        return DB::exec('DELETE FROM `groupsync` WHERE `group_id_mk`=?', [$group_id_mk]);
    }

    public function getGroupsyncByGroupIdMK($group_id_mk)
    {
        return DB::getRow('SELECT * FROM `groupsync` WHERE `group_id_mk`=?', [$group_id_mk]);
    }

    public function getAllGroupsyncAssoc(){
        $data = DB::getAssoc('SELECT * FROM `groupsync`');
        $newData = [];
        foreach ($data as $dat){
            $newData[$dat['group_id_mk']] = $dat;
        }
        return $newData;
    }

    public function getGroups()
    {
        $courses = MoyklassModel::getCourses(['includeClasses' => 'true']);
        return $courses;
    }

    public function getIndividualGroups(){
        return DB::exportAll(DB::find('groupsync', 'individual = "on"'));
    }


    /** Функция удаления не активных групп
     * Больше не актульано, архивные группы больше не нужно удалять
     * */
    public function deleteInactiveGroups(){

        $GroupsModel = new GroupsModel();

        $inactiveGroups = $GroupsModel->getGroups();
        $myGroups = $GroupsModel->getAllGroupsync();

        //var_dump($inactiveGroups);

        $myNewGroups = [];
        foreach ($myGroups as $myGroup) {
            $myNewGroups[] = $myGroup['group_id_mk'];
        }

        foreach ($inactiveGroups as $inactiveGroup) {
            foreach ($inactiveGroup['classes'] as $class) {
                if($class['status'] !== 'opened' and in_array($class['id'], $myNewGroups)){
                    // удаляем группу из настроек
                    $this->deleteGroupsyncByClassId($class['id']);
                }
            }
        }

        //var_dump($myNewGroups);

    }


}