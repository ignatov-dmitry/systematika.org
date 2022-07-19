<?php


namespace GKTOMK\Models;

use GKTOMK\Models\LeadsModel;
use \GKTOMK\Models\MoyklassModel;

/**
 * Модель обрабатывает заявку от клиента на отправку в МойКласс
 * */
class HandlerModel
{
    /**
     * @var \GKTOMK\Models\LeadsModel
     */
    private $LeadsModel;

    public function __construct()
    {
        $this->LeadsModel = new LeadsModel();
    }
    /*
     * Обработка записи юзера
     * */
    public function handle($id){
        // Нашли клиента в нашей БД
        $sys_user = $this->LeadsModel->getUserById($id);

        // Нашли клиента в МойКласс по емейлу
        $mk_user = $this->checkUser($sys_user['id']);

        // Нашли абонемент
        $subscription = $this->LeadsModel->findSubscriptionByOffer($sys_user['gk_offers']);
       // print_r( $subscription);
        // Определеяем сумму
        $summa = $sys_user['gk_cost_money'];
        if (!empty($subscription)) {
            if($summa < $subscription['price']){
                $summa = $subscription['price'];
            }
        }

        // Начислили клиенту деньги
        $this->setMoney($mk_user['id'], $summa);
        $status['status'] = 'moneyadd';
        $status['status_result'][] = 'Средства на счет зачислены.';


        // Создали абонемент и списали деньги со счета
        $sub = $this->createSubscription($mk_user['id'], $subscription, @$mk_user['joins'][0]['classId'], $summa);
        $status['status'] = 'success';
        $status['status_result'][] = 'Абонемент создан.';


        $this->resultHandle($id, $status);

    }
    /*
     * Проверяет, существует ли пользователь. Если нет, то создает нового
     * */
    private function checkUser($id){

        $sys_user = $this->LeadsModel->getUserById($id);

        $mk_user = $this->LeadsModel->getFindUserByEmail($sys_user['gk_email']);


        if (empty($mk_user) or (mb_strtolower($mk_user['email']) !== mb_strtolower($sys_user['gk_email']))){
            $dataCreate['name'] = $sys_user['gk_first_name'] . ' ' . $sys_user['gk_last_name'];

            if (!empty($sys_user['gk_email'])) {
                $dataCreate['email'] = $sys_user['gk_email'];
            }

            if (!empty($sys_user['gk_phone'])) {
                $dataCreate['phone'] = $sys_user['gk_phone'];
            }

            $mk_user = MoyklassModel::createUser($dataCreate);
            $status['status'] = 'usercreate';
            $status['status_result'][] = 'Юзер в MoyKlass создан.';
            $this->resultHandle($id, $status);
        }

        return $mk_user;
    }

    private function setMoney($mk_id_user, $summa){
        // Начисляем деньги на счет
        return MoyklassModel::createPaymentUser([
            'userId' => $mk_id_user,
            'date' => '' . date("Y-m-d", time()) . '',
            'summa' => intval($summa),
            'optype' => 'income',
            'paymentTypeId' => 1,
            'comment' => ''
        ]);
    }

    private function createSubscription($mk_user_id, $subscription, $classId, $summa){
        // Проверяем, есть ли у клиента в МойКласс участие в группах
        if (!empty($classId)) {

            if (!empty($subscription)) {

                // Создаем абонемент для клиента в МК
                $result_subscription = MoyklassModel::createUserSubscriptions([
                    'userId' => $mk_user_id,
                    'subscriptionId' => $subscription['id'], //24747,
                    'sellDate' => '' . date("Y-m-d", time()) . '',
                    'beginDate' => '' . date("Y-m-d", time()) . '', // Дата начала действия абонемента
                    'classIds' => [$classId],
                    'mainClassId' => $classId,

                ]);
                $status_result[] = 'Абонемент создан.';


                // Находим счет который автоматически был создан после создания абонемента
                $invoiceId = $this->LeadsModel->getInvoiceByUserSubscription($mk_user_id, $result_subscription['id']);

                // Делаем списание по счету за абонемент
                $result = MoyklassModel::createPaymentUser([
                    'userId' => $mk_user_id,
                    'date' => '' . date("Y-m-d", time()) . '',
                    'summa' => -$summa,
                    'optype' => 'debit',
                    'paymentTypeId' => 1,
                    'invoiceId' => $invoiceId,
                    'comment' => 'Тестовое списание за абонемент'
                ]);

                $status['status'] = 'success';
                $status['status_result'][] = 'Средства списаны со счета.';
                $status['result'] = $result;

                echo 'Чувак есть в группах. Выдаем абонемент!';
            } else { /// Абонемент не найден по офферу
                $status['status'] = 'error';
                $status['status_result'][] = 'Не найдено соответсвие абонемента и оффера.';
            }
        }else{
            $status['status'] = 'error';
            $status['status_result'][] = 'Не найден класс пользователя.';

        }
        return $status;
    }

    /*
     * Указывает статус обработки
     * */
    private function resultHandle($id, $status = []){
        $this->LeadsModel->setStatusUser($id, $status);
    }

}