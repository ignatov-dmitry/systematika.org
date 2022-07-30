<?php


namespace GKTOMK\Models;


class ScheduleactivitylogModel extends ScheduleModel
{

    public function __construct()
    {
        DB::init();
    }

    public function addAction($type, $create_member_id, $action_member_id, $action_object_id, $data = [])
    {
        $log = DB::dispense('scheduleactivitylog');
        $log->action_type = $type;
        $log->create_member_id = $create_member_id;
        $log->action_member_id = $action_member_id;
        $log->action_object_id = $action_object_id;
        $log->action_data = json_encode($data);
        $log->date_create = time();
        DB::store($log);
    }

    public function getActionByActionsMemberIdAndObjectId($member_id, $object_ids = [])
    {

        if(empty($object_ids))
            return [];



        $query = '';
        $data = [$member_id];
        $i = 1;
        foreach ($object_ids as $object_id) {
            $query .= '`sal`.`action_object_id`=?';
            $data[] = $object_id;
            if(count($object_ids)>$i){
                $query .= ' or ';
            }
            $i++;
        }
        $query = '&& ( ' . $query . ' )';

        $sql = 'SELECT `sal`.*,
                `m1`.`first_name` `create_member_first_name`,
                `m1`.`last_name` `create_member_last_name`,
                `m2`.`first_name` `action_member_first_name`,
                `m2`.`last_name` `action_member_last_name`
            FROM `scheduleactivitylog` `sal`
            LEFT JOIN `member` `m1` ON `sal`.`create_member_id`=`m1`.`id`
            LEFT JOIN `member` `m2` ON `sal`.`action_member_id`=`m2`.`id`
            WHERE `sal`.`action_member_id`=? ' . $query;

        return DB::getAll($sql,
            $data
        );
    }


}