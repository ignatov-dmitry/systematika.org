<?php


namespace GKTOMK\Models;


class HomeworklinksModel
{

    public function __construct()
    {
        DB::init();
    }

    public function editHomeworklink($data = [])
    {
        return DB::edit('homeworklinks', $data);
    }

    public function deleteHomeworklink($id)
    {
        return DB::delete('homeworklinks', 'id', $id);
    }


    public function getHomeworklinksAllAssoc(){
        $data = DB::getAll('SELECT * FROM `homeworklinks`');
        $newdata = [];
        foreach ($data as $datum) {
            $newdata[$datum['program_id']][] = $datum;
        }

        return $newdata;
    }

    public function getWomeworklinkByGroup($group){
        return DB::getRowByKey('homeworklinks', 'group', $group, ['link']);
    }

    public function findGroup($desc){
        preg_match('@\[([^[]*)\]@', $desc, $matches);
        if(isset($matches[1]))
            return @$matches[1];
        else
            return 0;
    }

}