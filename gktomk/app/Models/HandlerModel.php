<?php


namespace GKTOMK\Models;

/**
 * Модель обрабатывает заявку от клиента на отправку в МойКласс
 * */
class HandlerModel
{

    private $userId;
    protected $stopped;
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

    public function handle($userId){
        $this->userId = $userId;
        $this->startHandler();
        return $this;
    }

    /*
     * Возвращает статус обработки
     * */
    public function getStatus(){
        return $this->statusHandle;
    }

    /*
     * Обработка записи юзера
     * */
    private function startHandler()
    {

        // Нашли клиента в нашей БД
        $this->userSys = $this->LeadsModel->getUserById($this->userId);
        if(empty($this->userSys['id']))
            return 0;

        // Ищем нашего клиента в МК
        $this->loadUserMk();

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
     * Выполняет загрузку пользователя МК в класс
     * */

    private function loadUserMk()
    {

        $this->userMk = $this->LeadsModel->getFindUserByEmail($this->userSys['gk_email']);


        if (empty($this->userMk) or !isset($this->userMk['id']) or empty($this->userMk['id'])) {
            $userCreate = $this->createUser();
            if (!isset($userCreate['id']) or empty($userCreate['id'])) {
                $this->resultHandle(['status' => 'error_createuser', 'code' => 'createuser', 'text' => 'Ошибка при создании пользователя!', 'debug' => $this->userMk]);
                $this->stopped = 1;
                return;
            } else {
                $this->resultHandle(['status' => 'success', 'code' => 'createuser', 'text' => 'Пользователь создан!', 'debug' => $this->userMk]);
                $this->userMk = $userCreate;
            }
        }

    }


    /*
     * Проверяет, существует ли пользователь. Если нет, то создает нового
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
        return MoyklassModel::createUser($dataCreate);
    }

    private function resultHandle($result = [])
    {
        $this->LeadsModel->setStatusUser($this->userId, ['status' => $result['status']]);
        if (isset($result['debug']) and is_array($result['debug']))
            $result['debug'] = json_encode($result['debug']);
        $this->LeadsModel->addLogUser($this->userId, @$result['code'], @$result['text'], @$result['debug']);
        $this->statusHandle = $result['status'];
        return 1;
    }

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
            $this->resultHandle(['status' => 'error', 'code' => 'money', 'text' => 'Ошибка при начислении средств!', 'debug' => $result]);
            $this->stopped = 1;
            return;
        } else {
            $this->resultHandle(['status' => 'setmoney', 'code' => 'setmoney', 'text' => 'Денежные средства начислены!', 'debug' => $result]);
        }
    }

    /*
     * Указывает статус обработки
     * */

    private function createSubscription($subscription, $summa)
    {
        $classId = $this->userMk['joins'][0]['classId'];
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


}