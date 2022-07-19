<?php


namespace GKTOMK\Models;

/**
 * Модель обрабатывает заявку от клиента на отправку в МойКласс
 * */
class HandlerHwkModel extends HomeworkModel
{

    private $hwkData;
    private $hwkId;
    private $statusHandle = 'start';

    public function __construct()
    {
    }

    private function handle($hwkId)
    {
        $this->hwkId = $hwkId;
        $this->startHandler();
        return $this;
    }

    /*
     * Запускает обработку задачи
     * */

    private function startHandler()
    {
        $this->hwkData = $this->getHwkById($this->hwkId);

        if(!$this->hwkData or !$this->hwkData['id']){
            $this->resultHandle(['status' => 'error', 'code' => 'not found', 'text' => 'Не найдена запись лога домашнего задания!', 'debug' => $this->hwkData]);
            return 0;
        }
        //var_dump( $this->hwkData);


        $userMk = MoyklassModel::getUserById(['userId' => $this->hwkData['mk_user_id']]);

        if(!$userMk or !$userMk['id']){
            $this->resultHandle(['status' => 'error', 'code' => 'mk user not found', 'text' => 'Пользователь МК не найден', 'debug' => [$this->hwkData, $userMk]]);
            return 0;
        }

        //var_dump($userMk);


        $lesson = MoyklassModel::getLesson($this->hwkData['mk_lesson_id'], $this->hwkData['mk_user_id']);

        if(!$lesson and !$lesson['id']){
            $this->resultHandle(['status' => 'error', 'code' => 'lesson not found', 'text' => 'Урок не найден', 'debug' => [$this->hwkData, $lesson]]);
            return 0;
        }

        $groupGK = $this->findGroup($lesson['description']);

        $GetcourseModel = new GetcourseModel();
        $result = $GetcourseModel->createUser([
            'email' => $userMk['email'],
            'group' => $groupGK,
        ]);

        if($result and $result->success){
            $this->resultHandle(['status' => 'success', 'code' => 'success', 'text' => 'Запись передана в ГК', 'debug' => [$result]]);
            $this->setHwk($this->hwkId, ['email' => $userMk['email'], 'gk_uid' => $result->result->user_id, 'group'=>$groupGK]);
        }else{
            $this->resultHandle(['status' => 'error', 'code' => 'error update gk user', 'text' => 'Ошибка при создании пользователя в ГК', 'debug' => [$result]]);
        }

        return $result;

    }

    /*
     * Обработка записи домашнего задания
     * */

    public function getStatus()
    {
        return $this->statusHandle;
    }


    public function cronHandle()
    {
        $Hwks = $this->getCronHwk();

        foreach ($Hwks as $Hwk) {

            echo $Hwk->id . ' загружен <br/>';
            echo '<br/> Статус обработки:' . $this->handle($Hwk->id)->getStatus();
        }
    }

    private function resultHandle($result = [])
    {
        $this->setHwk($this->hwkId, ['status' => $result['status']]);
        if (isset($result['debug']) and is_array($result['debug']))
            $result['debug'] = json_encode($result['debug']);

        $this->addLogHwk($this->hwkId, @$result['code'], @$result['text'], @$result['debug']);
        $this->statusHandle = $result['status'];
        return 1;
    }


}