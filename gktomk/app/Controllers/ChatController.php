<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\ChatModels\ChatUserModel;
use GKTOMK\Models\ChatModels\ChatTeacherModel;
use GKTOMK\Models\DB;
use GKTOMK\Models\MoyklassModel;

class ChatController extends Controller
{

    /**
     * @var ChatModel
     */
    private $chatModel;
    private $chatTeacherModel;
    /**
     * @var ChatUserModel
     */
    private $chatUserModel;

    public function __construct()
    {
        parent::__construct();
        $this->chatUserModel = new ChatUserModel();
        $this->chatTeacherModel = new ChatTeacherModel();
        $this->Member->is_auth();
        $this->View->setVar('MEMBER', $this->Member->getMemberData());
    }

    public function main()
    {
        $this->View->parseTpl('chat/index', false)->parseTpl('chat/main')->output();
    }

    public function getTeacherDialogsAjax(){
        //$res = $this->chatTeacherModel->getOpenDialogsTeacher('33786');
        $res = $this->chatTeacherModel->getContactsTeacher('48155');
        print_r($res);
    }

    public function getDialogsAjax(){

        if(!empty($this->Member->getMemberData()['mk_manager_id'])){

            $dialog_user_id = $this->Member->getMemberData()['mk_manager_id'];

            $res = $this->chatTeacherModel->getContactsTeacher($dialog_user_id);
            
        }else{
            $dialog_user_id = $this->Member->getMemberData()['mk_uid'];
            $res = $this->chatUserModel->getUserDialogsByUserIdMK($dialog_user_id);

        }

        $data = [
            'dialogs' => $res,
        ];


        return json_encode($data);
    }

    public function getMessagesAjax(){

        if(!empty($this->Member->getMemberData()['mk_manager_id'])){
            //echo $this->Member->getMemberData()['mk_manager_id'];
            $manager_id = $this->Member->getMemberData()['id'];
            $client_id = $_GET['client_id']; // Потом заменить для разных ролей (препод, админ; сейчас только юзер)
            //echo $manager_id.' - '.$client_id;
        }else{
            $manager_id = $_GET['manager_id'];
            $client_id = $this->Member->getMemberId(); // Потом заменить для разных ролей (препод, админ; сейчас только юзер)
        }



        //$res = $this->chatModel->getLoadMessages($manager_id, $client_id);
        $res = $this->chatUserModel->getLoadMessages($manager_id, $client_id);

        return json_encode($res);
    }

    public function postMessageAjax(){
        $from_member_id = $this->Member->getMemberId();

        $dialog_id = $_POST['dialog_id'];



        //$dialog = $this->chatModel->getFindDialog($manager_id, $client_id);

        return $this->chatUserModel->addMessageByMemberIdAndDialogId(
            $from_member_id,
            $dialog_id,
            [
                'message' => $_POST['message']
            ]
        );
    }

    public function postFileAjax(){
        print_r($_FILES);
        $from_member_id = $this->Member->getMemberId();

        $dialog_id = 1;
        $result = $this->chatUserModel->uploadAttachment($dialog_id);

        if(empty($result['error'])) {
            $this->chatUserModel->addMessageByMemberIdAndDialogId(
                $from_member_id,
                $dialog_id,
                [
                    'message' => $result['attachment_name'],
                    'attachment_id' => $result['attachment_id']
                ]
            );
        }

        header('Content-Type: application/json');
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function getAttach(int $message_id){
        $this->chatUserModel->getDownloadAttachmentByMessageId($message_id);
    }

    public function getTest(){
        $this->chatUserModel->getDialogInfo(1);
    }

    public function postCallAdminAjax(){
        $this->chatTeacherModel->setDialog($_POST['dialog_id'], 'calladmin', $_POST['call']);
    }

    public function getChatSync()
    {
        $groupsync = [];
        if (isset($_GET['group_id_mk']))
        {
            $group_id_mk = $_GET['group_id_mk'];
            $groupsyncs = DB::getAll("SELECT * FROM groupsync where group_id_mk = '{$group_id_mk}'");

           for ($i = 0; $i < count($groupsyncs); $i++ )
           {
               $groupsyncs[$i]['manager_ids'] = @$groupsyncs[$i]['manager_ids'] ?: '[]';
           }

            $this->View->setVar('SYNCS', $groupsyncs);
        }

        $managers = MoyklassModel::getManagers();

        usort($managers, function ($a, $b){
            return strcmp($a['name'], $b['name']);
        });

        $mangerIds = array_column($managers, 'id');


        $this->View->setVar('MANAGERS', $managers);
        $this->View->setVar('MANAGER_IDS', $mangerIds);
        $this->View->parseTpl('chat/chatsync', false)->parseTpl('main')->output();
    }

    public function postChatSync()
    {
        $managers_ids = json_encode($_POST['managers']);
        $groupsync = DB::load('groupsync', $_POST['id']);
        $groupsync['manager_ids'] = $managers_ids;
        DB::store($groupsync);

        $this->getChatSync();
    }
}