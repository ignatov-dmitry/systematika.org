<?php


namespace GKTOMK\Controllers;


use GKTOMK\Models\ChatModels\ChatAdminModel;
use GKTOMK\Models\ChatModels\ChatTeacherModel;
use GKTOMK\Models\ChatModels\ChatUserModel;
use GKTOMK\Models\LessonsModel;
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
    /**
     * @var ChatAdminModel
     */
    private $chatAdminModel;

    public function __construct()
    {
        parent::__construct();
        $this->chatUserModel = new ChatUserModel();
        $this->chatTeacherModel = new ChatTeacherModel();
        $this->chatAdminModel = new ChatAdminModel();
        $this->Member->is_auth();
        $this->View->setVar('MEMBER', $this->Member->getMemberData());
    }

    public function main()
    {
        $this->View->parseTpl('chat/index', false)->parseTpl('chat/main')->output();
    }

    public function getTeacherDialogsAjax()
    {
        //$res = $this->chatTeacherModel->getOpenDialogsTeacher('33786');
        $res = $this->chatTeacherModel->getContactsTeacher('48155');
        print_r($res);
    }

    public function getDialogsAjax()
    {

        // Для преподавателей
        if (!empty($this->Member->getMemberData()['mk_manager_id']) and !$this->Member->isAccess(1)) {
            $dialog_user_id = $this->Member->getMemberData()['mk_manager_id'];
            $res = $this->chatTeacherModel->getContactsTeacher($dialog_user_id);
            $data = [
                'dialogs' => $res,
            ];
            // Для пользователей
        } elseif (!$this->Member->isAccess(1)) {
            $dialog_user_id = $this->Member->getMemberData()['mk_uid'];
            $res = $this->chatUserModel->getUserDialogsByUserIdMK($dialog_user_id);
            $data = [
                'dialogs' => $res,
            ];
        }
        // Для админов
        if ($this->Member->isAccess(1)) {
            $res = $this->chatAdminModel->getTeacherDialogsOpenForAdminsByDateUpdate(0);
            $data = [
                'dialogs' => $res
            ];
        }


        //var_dump($data);
        return json_encode($data);
    }

    public function getTeachersAjax()
    {
        $res = $this->chatAdminModel->getAllTeachers();
        $data = [
            'teachers' => $res
        ];
        return json_encode($data);
    }

    public function getDialogsOpenAjax()
    {
        $data = [];
        // Для преподавателей
        if (!empty($this->Member->getMemberData()['mk_manager_id']) and !$this->Member->isAccess(1)) {
            $dialog_member_id = $this->Member->getMemberData()['id'];
            $res = $this->chatTeacherModel->getTeacherDialogsOpenByManagerMemberId($dialog_member_id, (int)$_GET['date_update']);
            $data = [
                'dialogs' => $res,
            ];
            // Для пользователей
        } elseif (!$this->Member->isAccess(1)) {
            $dialog_member_id = $this->Member->getMemberData()['id'];
            $res = $this->chatUserModel->getUserDialogsOpenByClientMemberId($dialog_member_id, (int)$_GET['date_update']);
            $data = [
                'dialogs' => $res
            ];
        }
        if ($this->Member->isAccess(1)) {
           /* $dialog_member_id = $this->Member->getMemberByMkManagerId($_GET['mk_manager_id'])['id'];
            $res = $this->chatAdminModel->getTeacherDialogsOpenForAdminsByManagerMemberId($dialog_member_id, (int) $_GET['date_update']);
            */
            //$dialog_member_id = $this->Member->getMemberByMkManagerId($_GET['mk_manager_id'])['id'];
            $res = $this->chatAdminModel->getTeacherDialogsOpenForAdminsByDateUpdate((int) $_GET['date_update']);
            $data = [
                'dialogs' => $res
            ];
        }


        return json_encode($data);
    }

    public function getMessagesAjax()
    {

        if (!empty($this->Member->getMemberData()['mk_manager_id']) and !$this->Member->isAccess(1)) {
            //echo $this->Member->getMemberData()['mk_manager_id'];
            $manager_id = $this->Member->getMemberData()['id'];
            $client_id = $_GET['client_id']; // Потом заменить для разных ролей (препод, админ; сейчас только юзер)
            //echo $manager_id.' - '.$client_id;
        } elseif (!$this->Member->isAccess(1)) {
            $manager_id = $_GET['manager_id'];
            $client_id = $this->Member->getMemberId(); // Потом заменить для разных ролей (препод, админ; сейчас только юзер)
        } elseif ($this->Member->isAccess(1)) {
            $client_id = $_GET['client_id'];
            $manager_id = $_GET['manager_id'];
        }


        //$res = $this->chatModel->getLoadMessages($manager_id, $client_id);
        $res = $this->chatUserModel->getLoadMessages($manager_id, $client_id);
        // Помечаем сообщения которые нам прислали, прочитанными
        if (!$this->Member->isAccess(1))
            $this->chatUserModel->setReadMessagesByDialogIdAndFromId($res['dialog_id'], $this->Member->getMemberId());

        return json_encode($res);
    }

    public function postMessageAjax()
    {
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

    public function postFileAjax()
    {

        $from_member_id = $this->Member->getMemberId();

        $dialog_id = $_POST['dialog_id'];
        $data = $this->chatUserModel->uploadAttachment($dialog_id);

        foreach ($data as $result)
        {
            if (empty($result['error'])) {
                $this->chatUserModel->addMessageByMemberIdAndDialogId(
                    $from_member_id,
                    $dialog_id,
                    [
                        'message' => $result['attachment_name'],
                        'attachment_id' => $result['attachment_id']
                    ]
                );
            }
        }


        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit();
    }

    public function getAttach(int $message_id)
    {
        $this->chatUserModel->getDownloadAttachmentByMessageId($message_id);
    }

    public function getTest()
    {
        $this->chatUserModel->getDialogInfo(1);
    }

    public function postCallAdminAjax()
    {
        if ($_POST['call'] == 1) {
            $this->chatTeacherModel->setCallAdminByDialogId($_POST['dialog_id']);
        } else {
            $this->chatTeacherModel->setUnCallAdminByDialogId($_POST['dialog_id']);
        }
    }

    public function getSyncManagers()
    {
        $managers = MoyklassModel::getManagers();
        foreach ($managers as $manager) {
            $member = $this->Member->getMemberByEmail($manager['email']);
            if (!empty($member['id']))
                $this->Member->setUpdateMember(['id' => $member['id'], 'mk_manager_id' => $manager['id']]);
            //print_r($member);
        }
        //print_r($managers);

    }

    public function getDebug($email = 'string')
    {
        $member = $this->Member->getMemberByEmail($email);

        echo 'Пользователь: <br/><br/>';
        print_r($member);

        echo '<br/><br/>Уроки пользователя: <br/><br/>';
        $LessonsModel = new LessonsModel();
        $lessons = $LessonsModel->getLessonsByUserIdMKAndTime($member['mk_uid']);
        print_r($lessons);


        echo '<br/><br/>Группы пользователя: <br/>';
        $classes = $this->chatUserModel->getClassesUserByMkUserId($member['mk_uid']);
        print_r($classes);

        echo '<br/><br/>Диалоги пользователя: <br/>';
        $dialogs = $this->chatUserModel->getUserDialogsByUserIdMK($member['mk_uid']);
        print_r($dialogs);
    }

}