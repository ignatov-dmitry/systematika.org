<?php


namespace GKTOMK\Models\ChatModels;


use GKTOMK\Models\DB;
use GKTOMK\Models\MemberModel;
use GKTOMK\Models\MoyklassModel;

class ChatAdminModel extends ChatTeacherModel
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getDialogsOpenedForAdmin(){
        return DB::getAll('SELECT * FROM `chatdialogs`');
    }

    public function getAllTeachers(){
        return DB::getAll('SELECT `id`,`first_name`,`last_name`,`email`,`mk_uid`,`mk_manager_id`,`foto_url` FROM `member` WHERE `mk_manager_id` IS NOT NULL');
    }

    public function getTeacherDialogsOpenForAdminsByManagerMemberId($manager_member_id, $date_update=0){
        $dialogs = $this->getDialogsByManagerMemberId($manager_member_id);
        $output = [];
        foreach ($dialogs as $dialog) {
            $output[$dialog['id']] = $this->getDialogInfo($dialog['id']);
            if($output[$dialog['id']]['date_update_admin']<=$date_update){
                unset($output[$dialog['id']]);
            }
        }
        return $output;
    }

    public function getOpenDialogsForAdminsByLastMessage()
    {
        return DB::getAll('SELECT cd.*, cm.*, cd.id id
                        FROM chatdialogs AS cd
                        INNER JOIN chatmessages AS cm ON cm.id = (
                            SELECT id
                            FROM chatmessages AS cm2
                            WHERE cm2.chatdialog_id = cd.id
                            
                            ORDER BY time DESC
                            LIMIT 1
                        )
                        WHERE 
                        (cm.read=0 &&
                            cd.client_member_id=cm.from_member_id )
                       
                            OR cd.calladmin=1
                            
                            ORDER by time DESC, date_update DESC, calladmin DESC', ['time' => (time()-(60*25))]); //&& cm2.read=0
        /* OR
                        (
                        cd.date_update_admin > (:time)
                        or
                        cd.date_update_manager > (:time)
                            )*/
    }

    public function getTeacherDialogsOpenForAdminsByDateUpdate($date_update){
        $dialogs = $this->getOpenDialogsForAdminsByLastMessage();
        //var_dump($dialogs);
        $output = [];
        $i = 0;
        foreach ($dialogs as $dialog) {
            $output[$i] = $this->getDialogInfo($dialog['id']);
           // var_dump($output[$i]);
            if($output[$i]['date_update_admin']<=$date_update or empty($output[$i]['groups'])){
                unset($output[$i]);
            }
            $i++;
        }
        //var_dump($output);
        return $output;
    }

    public function getSyncManagers()
    {
        $MemberModel = new MemberModel();
        $managers = MoyklassModel::getManagers();
        //var_dump($managers);
        foreach ($managers as $manager) {
            $member = $MemberModel->getMemberByEmail($manager['email']);
            if (!empty($member['id']))
                $MemberModel->setUpdateMember(['id' => $member['id'], 'mk_manager_id' => $manager['id']]);
            //print_r($member);
        }
        //print_r($managers);

    }

}