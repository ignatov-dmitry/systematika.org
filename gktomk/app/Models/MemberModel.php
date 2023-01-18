<?php
/*
 * Модель пользователей. Хранит информации о связках клиентов из gk и mk
 * */

namespace GKTOMK\Models;


class MemberModel
{

    /**
     * @var array
     */
    private $_data;
    private $_id;

    public function __construct()
    {
        DB::init();
    }

    /*
     * Обновляет данные пользователя по gk_uhash, если он существует, если нет - создает
     * */
    public function updateMemberByGkUhash($data = [])
    {
        if(empty($data['gk_uhash']))
            return;
        $getMember = $this->getMemberByGkUserHash($data['gk_uhash']);
        var_dump($getMember);
        if (!empty($getMember['id'])) {
            $data['id'] = $getMember['id'];
            return $this->setUpdateMember($data);
        }
        return $this->setCreateMember($data);
    }

    public function getMemberByGkUserHash($hash)
    {
        return $this->getMember('gk_uhash', $hash);
    }

    public function getMemberByMkUid($user_id_mk)
    {
        return $this->getMember('mk_uid', $user_id_mk);
    }

    private function getMember($key, $value)
    {
        //return DB::findOne('member', "`" . $key . "`=:" . $key, ['' . $key . '' => $value]);
        $result = DB::getAll("SELECT * FROM `member` WHERE `" . $key . "`=:" . $key, ['' . $key . '' => $value])[0];
        //var_dump($result);
        return $result;
    }

    public function setUpdateMember($data = [])
    {
        // Если ид не указан, создаем
        /*if(!$data['id'])
            return $this->setCreateMember($data);*/

        $member = DB::load('member', $data['id']);
        unset($data['id']);
        foreach ($data as $key => $value) {
            $member->{$key} = $value;
        }
        return DB::store($member);
        //return $id;
    }

    public function setCreateMember($data = [])
    {
        $member = DB::dispense('member');
        foreach ($data as $key => $value) {
            $member->$key = $value;
        }
        return DB::store($member);
        //$member;
    }

    /*
     * Отдает mk user id если есть, если нет, запрашивает создание клиента в mk
     * */

    public function getMemberByGkUserId($gk_uid)
    {
        return $this->getMember('gk_uid', $gk_uid);
    }

    public function getMemberByEmail($email)
    {
        return $this->getMember('email', $email);
    }

    public function getMemberParamMkUid($memberId = 0)
    {
        if (!$memberId)
            $memberId = $this->_id;

        $member = $this->getMember('id', $memberId);

        if (!empty($member['mk_uid']))
            return $member['mk_uid'];

        return $this->sendMemberToMoyKlass($member);

    }

    private function sendMemberToMoyKlass($member)
    {

        $mk_user = MoyklassModel::getFindUserByEmail($member['email']);

        // Если пользователь в МК не найден, создаем нового
        if (empty($mk_user) or $mk_user == null) {

            $dataCreate['name'] = $member['first_name'] . ' ' . $member['last_name'];

            if (!empty($member['email'])) {
                $dataCreate['email'] = $member['email'];
            }

            if (!empty($member['gk_phone'])) {
                $dataCreate['phone'] = $member['phone'];
            }

            $mk_user = MoyklassModel::createUser($dataCreate);
        }

        // Сохраняем mk_uid в мембера
        $this->setUpdateMember([
            'id' => $member['id'],
            'mk_uid' => $mk_user['id'],
        ]);

        // Отдаем id мембера
        return $mk_user['id'];
    }

    /*
     * Отправляет пользователя в мойкласс и сохраняет ИД в мембера
     * */

    public function sendMemberToMoyKlassById($memberId)
    {
        return $this->sendMemberToMoyKlass($this->getMember('id', $memberId));
    }

    public function session_start()
    {
        session_start();
    }

    public function is_auth()
    {
        if (!empty($_GET['password']))
            $_SESSION['password'] = $_GET['password'];

        $member = $this->getMember('gk_uhash', $_SESSION['password']);

        if (!$member['id'] and isset($_GET['email'])) { //Если указан email, отправляем в геткурс запрос на создание пользователя
            $this->is_not_found($_GET['email']);
            //var_dump($result);
            die('Доступ к расписанию будет  доступен в течение 5-10 минут.<br/>Зайдите позже.');
        }

        if (!$member['id'])
            die('Доступ запрещен! <br/>Ключ доступа: ' . $_REQUEST['password']);

        $this->_id = $member['id'];
        $this->_data = $member;
    }

    public function is_not_found($email)
    {


        $get = $this->getMemberByEmail($email);

        if(!empty($get['id']))
            return true;

        $GetCourse = new GetcourseModel();
        $result =  $GetCourse->init([
            'email' => $email,
            'groups' => [
                '[MK] Добавить в интеграцию'
            ]])->send();

        return false;
    }

    /**
     * Возврщает данные от текущего пользователя
     * */
    public function getMemberData($memberId = 0)
    {
        if (!$memberId)
            return $this->_data;
        else
            return $this->getMember('id', $memberId);
    }

    public function getMemberId()
    {
        return $this->_id;
    }

    public function getId()
    {
        return $this->_id;
    }

    public function isAccess($access, $close = 0)
    {
        if ($this->_data['access'] < $access) {
            if ($close == true)
                die('Not access');
            else
                return false;
        }
        return true;
    }


}
