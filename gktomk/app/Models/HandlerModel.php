<?php


namespace GKTOMK\Models;

/**
 * Модель обрабатывает заявку от клиента на отправку в МойКласс
 * */
class HandlerModel
{

    protected $stopped;
    private $userId;
    /**
     * @var LeadsModel
     */
    private $LeadsModel;
    /**
     * Данные о пользователе
     * */
    private $userSys;
    private $userMk;
    private $statusHandle;

    public function __construct()
    {
        $this->LeadsModel = new LeadsModel();
    }

    public function handle($userId)
    {
        $this->userId = $userId;
        $this->startHandler();
        return $this;
    }

    /*
     * Возвращает статус обработки
     * */

    private function startHandler()
    {

        // Нашли клиента в нашей БД
        $this->userSys = $this->LeadsModel->getUserById($this->userId);
        if (empty($this->userSys['id']))
            return 0;

        // Ищем нашего клиента в МК
        $this->loadUserMk();

        // Сохраняем текущий баланс
        $this->LeadsModel->setUser($this->userSys['id'], ['balans' => $this->userMk['balans']]);

        // Добавляем пользователя в стартовую группу
        if (!$this->stopped) // Делаем проверку на тот случай, если произошла какая-то ошибка
            $this->addStartGroup();

        // Нашли абонемент
        $subscription = $this->LeadsModel->findSubscriptionByOffer($this->userSys['gk_offers']);
        // Определеяем сумму
        $summa = $this->userSys['gk_cost_money'];
        if (!empty($subscription)) {
            if ($summa < $subscription['price']) {
                $summa = $subscription['price'];
            }
        }

        // Начислиляем клиенту деньги
        if (!$this->stopped)
            $this->setMoney($summa);

        // Создаем абонемент и списываем деньги со счета
        if (!$this->stopped)
            $this->createSubscription($subscription, $summa);

    }

    /*
     * Обработка записи юзера
     * */

    private function loadUserMk()
    {

        $this->userMk = $this->LeadsModel->getFindUserByEmail($this->userSys['gk_email']);


        if (empty($this->userMk) or !isset($this->userMk['id']) or empty($this->userMk['id'])) {
            $this->resultHandle(['status' => 'notice', 'code' => 'usernotfound', 'text' => 'Пользователь не найден!', 'debug' => $this->userMk]);
            $userCreate = $this->createUser();
            if (!isset($userCreate['id']) or empty($userCreate['id'])) {
                $this->resultHandle(['status' => 'error_createuser', 'code' => 'createuser', 'text' => 'Ошибка при создании пользователя!', 'debug' => $userCreate]);
                $this->stopped = 1;
                return;
            } else {
                $this->userMk = $userCreate;
                $this->resultHandle(['status' => 'success', 'code' => 'createuser', 'text' => 'Пользователь создан!', 'debug' => $this->userMk]);

            }
        }

    }


    /*
     * Выполняет загрузку пользователя МК в класс
     * */

    private function resultHandle($result = [])
    {
        $this->LeadsModel->setStatusUser($this->userId, ['status' => $result['status']]);
        if (isset($result['debug']) and is_array($result['debug']))
            $result['debug'] = json_encode($result['debug']);
        $this->LeadsModel->addLogUser($this->userId, @$result['code'], @$result['text'], @$result['debug']);
        $this->statusHandle = $result['status'];
        return 1;
    }


    /*
     * Непосредственно создает нового пользователя
     * */

    private function createUser()
    {
        $dataCreate['name'] = $this->userSys['gk_first_name'] . ' ' . $this->userSys['gk_last_name'];

        if (!empty($this->userSys['gk_email'])) {
            $dataCreate['email'] = $this->userSys['gk_email'];
        }

        if (!empty($this->userSys['gk_phone'])) {
            $dataCreate['phone'] = $this->userSys['gk_phone'];
        }
        $result = MoyklassModel::createUser($dataCreate);

        //{"code":"RequestValidationError","message":"\/phone: pattern should match pattern \"^[0-9]{10,15}$\""}
        // Если номер не приняли, создаем юзера без номера
        if ($result['code'] and $result['code'] == 'RequestValidationError') {
            unset($dataCreate['phone']);
            $result = MoyklassModel::createUser($dataCreate);
        }

        return $result;
    }

    /**
     * Метод добавляет пользователя в стартовую группу
     */
    public function addStartGroup()
    {

        // Не добавляем в группу, если функция отключена
        if (CONFIG['startGroup'] <= 0)
            return 0;

        $this->loadUserMk();

        // Добавляем в группу в том случае, если пользователя нет в группах вообще
        //$classId = $this->userMk['joins'][0]['classId'];
        $classId = $this->getUserJoinIdDontIndividual($this->userMk['joins']); // Исключает индивидуальные группы
        if (empty($this->userMk['joins']) or empty($classId)) {

            $result = MoyklassModel::setJoins(['userId' => $this->userMk['id'], 'classId' => intval(CONFIG['startGroup']), 'statusId' => 2]);
            if (isset($result['id']) and !empty($result['id'])) { // Успешно создано
                $this->resultHandle(['status' => 'addstartgroup', 'code' => 'addstartgroup', 'text' => 'Добавлен в стартовую группу!', 'debug' => $result]);
            } else { // Произошла какая-то ошибка
                $this->resultHandle(['status' => 'error_addstartgroup', 'code' => 'addstartgroup', 'text' => 'Ошибка при добавлении в стартовую группу!', 'debug' => $result]);
            }

        } elseif ($this->checkGroupStatusRecordedByJoins($this->userMk['joins'], CONFIG['statusGroup']['recorded'])) { // Случай, когда есть запись в группу со статусом записан
            $classId = $this->checkStatusLessonRecordedForlast14dayByUserId($this->userMk['id']);
            if(!empty($classId)){
                // Выдаем доступ к группе
                $result = MoyklassModel::setJoins(['userId' => $this->userMk['id'], 'classId' => intval($classId), 'statusId' => 2]);
                if (isset($result['id']) and !empty($result['id'])) { // Успешно создано
                    $this->resultHandle(['status' => 'addstartgroup', 'code' => 'addstartgroup', 'text' => 'Добавлен группу тест группу за 14 дней - ID'.$classId, 'debug' => $result]);
                } else { // Произошла какая-то ошибка
                    $this->resultHandle(['status' => 'error_addstartgroup', 'code' => 'addstartgroup', 'text' => 'Ошибка при добавлении тест группу за 14 дней!', 'debug' => $result]);
                }
            }
        }

    }

    private function getUserJoinIdDontIndividual($joins)
    {
        $result_classId = 0;
        foreach ($joins as $join) {
            $class = MoyklassModel::getClassById($join['classId']);
            if ($class['courseId'] !== 0) {
                $result_classId = $join['classId'];
                break;
            }
        }
        return $result_classId;
    }

    private function checkGroupStatusRecordedByJoins($joins = [], $statusId)
    {
        foreach ($joins as $join) {
            if ($join['statusId'] == $statusId)
                return $join['id'];
        }
        return 0;
    }

    /*
        * Возвращает ID группы по занятиям которые посетил человек
        * */
    private function checkStatusLessonRecordedForlast14dayByUserId($userId){

        $days = 60 * 60 * 24 * 14;

        $nowTime = time();
        $period1 = date('Y.m.d', $nowTime);
        $period2 = date('Y.m.d', ($nowTime-$days));

        $lessons = MoyklassModel::getLessons(['userId'=>$userId, 'includeRecords' => 'false', 'date[0]' => '2021.01.14',
            'date[1]' => '2021.01.27']);

        foreach ($lessons['lessons'] as $lesson) {

            foreach ($lesson['records']  as $record) {
                if($record['test'] == 1)
                    $classIds[] = $lessons['classId'];
            }
        }

        if(!empty($classIds) and count($classIds) == 1)
            return $classIds[0];

        return 0;
    }

    /*
     * Указывает статус обработки
     * */

    private function setMoney($summa)
    {

        // Начисляем деньги на счет
        $result = MoyklassModel::createPaymentUser([
            'userId' => $this->userMk['id'],
            'date' => '' . date("Y-m-d", time()) . '',
            'summa' => intval($summa),
            'optype' => 'income',
            'paymentTypeId' => 1,
            'comment' => ''
        ]);

        if (empty($result) or !isset($result['id']) or empty($result['id'])) {
            $this->resultHandle(['status' => 'error_setmoney', 'code' => 'setmoney', 'text' => 'Ошибка при начислении средств!', 'debug' => $result]);
            $this->stopped = 1;
            return;
        } else {
            $this->resultHandle(['status' => 'setmoney', 'code' => 'setmoney', 'text' => 'Денежные средства начислены!', 'debug' => $result]);
        }
    }


    /*
   * Функция исключает из списка заявок на группы - индивидуальные
   * Это нужно, чтоб не давать абонементы на индивидуальные занятия
   * */

    private function createSubscription($subscription, $summa)
    {
        $this->loadUserMk(); // Обновляем данные о пользователе

        //$classId = $this->userMk['joins'][0]['classId']; - старое
        $classId = $this->getUserJoinIdDontIndividual($this->userMk['joins']); // Исключает индивидуальные группы

        // Проверяем, есть ли у клиента в МойКласс участие в группах
        if (empty($classId)) {
            $this->resultHandle(['status' => 'error_createsubscription', 'code' => 'createsubscription', 'text' => 'Ошибка при создании абонемента - не указана группа!', 'debug' => $classId]);
            return 0;
        }

        if (empty($subscription)) {
            $this->resultHandle(['status' => 'error_createsubscription', 'code' => 'createsubscription', 'text' => 'Ошибка при создании абонемента - не указан абонемент!', 'debug' => $subscription]);
            return 0;
        }

        // Создаем абонемент для клиента в МК
        $result_subscription = MoyklassModel::createUserSubscriptions([
            'userId' => $this->userMk['id'],
            'subscriptionId' => $subscription['id'], //24747,
            'sellDate' => '' . date("Y-m-d", time()) . '',
            'beginDate' => '' . date("Y-m-d", time()) . '', // Дата начала действия абонемента
            'classIds' => [$classId],
            'mainClassId' => $classId,
        ]);

        if (empty($result_subscription) or !isset($result_subscription['id']) or empty($result_subscription['id'])) {
            $this->resultHandle(['status' => 'error_createsubscription', 'code' => 'createsubscription', 'text' => 'Ошибка при создании абонемента!', 'debug' => $result_subscription]);
            $this->stopped = 1;
            return 0;
        } else {
            $this->resultHandle(['status' => 'createsubscription', 'code' => 'createsubscription', 'text' => 'Абонемент создан!', 'debug' => $result_subscription]);
        }


        // Находим счет который автоматически был создан после создания абонемента
        $invoiceId = $this->LeadsModel->getInvoiceByUserSubscription($this->userMk['id'], $result_subscription['id']);

        // Делаем списание по счету за абонемент
        $result_payment = MoyklassModel::createPaymentUser([
            'userId' => $this->userMk['id'],
            'date' => '' . date("Y-m-d", time()) . '',
            'summa' => -$summa,
            'optype' => 'debit',
            'paymentTypeId' => 1,
            'invoiceId' => $invoiceId,
            'comment' => 'Списание за абонемент'
        ]);

        if (empty($result_payment) or !isset($result_payment['id']) or empty($result_payment['id'])) {
            $this->resultHandle(['status' => 'error_createsubscription', 'code' => 'createsubscription', 'text' => 'Ошибка при списании средств!', 'debug' => $result_payment]);
            return 0;
        } else {
            $this->resultHandle(['status' => 'success', 'code' => 'createsubscription', 'text' => 'Средства за абонемент списаны!', 'debug' => $result_payment]);
            return 1;
        }


    }

    /*
     * Узнает, есть ли заявки в группы с нужным статусом
     * */

    public function getStatus()
    {
        return $this->statusHandle;
    }


}