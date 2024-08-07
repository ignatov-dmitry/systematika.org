<?php
// Модель для работы с лидами. Сохраняет и получает лиды

namespace GKTOMK\Models;

class LeadsModel
{
    public function __construct()
    {
        DB::init();
    }

    /**
     * Метод по сумме находит абонемент и отдает его ID. Нужен для синхронизации заказов с ГК и абонементов в МК
     */
    public static function getSubscriptionBySumm($summ)
    {
        $subscriptions = MoyklassModel::getSubscriptions();
        foreach ($subscriptions['subscriptions'] as $subscription) {
            if ($subscription['price'] == $summ) {
                $subscriptionId = $subscription['id'];
                break;
            }
        }
        return $subscriptionId;
    }

    /**
     * Метод находит абонемент по офферу и отдает его ID. Нужен для синхронизации заказов с ГК и абонементов в МК
     * @param $offer_id
     * @return mixed
     */
    /*
    public static function findSubscriptionByOffer($offer_id)
    {
        if (!defined('CONFIG')) Config::init();
        if (empty($subscriptionId = @CONFIG['offer_to_subscription'][$offer_id])) {
            return 0;
        }

        $find = 0;
        $subscription = MoyklassModel::getSubscription($subscriptionId);
        if (empty($subscription) or !empty($subscription['code']) or empty($subscription['id'])) {
            return 0;
        }

        return $subscription;
    }
    */
    public static function phoneBlocks($number)
    {
        $add = '';
        if (strlen($number) % 2) {
            $add = $number[0];
            $number = substr($number, 1, strlen($number) - 1);
        }
        return $add . implode("-", str_split($number, 2));
    }

    /*
     * Отдает список всех пользователей
     * */

    public function createUser($data)
    {
        // Оставляем только цифры в номере телефона
        $data['phone'] = @preg_replace('/[^0-9]/', '', $data['phone']); // Убираем из номера +


        $users = DB::dispense('users');
        $users->gk_order = $data['order_id'];
        $users->gk_uid = $data['uid'];
        $users->gk_first_name = $data['first_name'];
        $users->gk_last_name = $data['last_name'];
        $users->gk_email = $data['email'];
        $users->gk_phone = $data['phone'];
        $users->gk_cost_money = $data['cost_money'];
        $users->gk_offers = $data['offers'];
        $users->date_add = time();
        $users->date_update = time();
        $users->status = 'new';
        $users->status_result = '';
        $users->gk_comment = $data['comment'];
        return DB::store($users);
    }

    public function getAllUsers($args = array())
    {
        $offset = 0;
        if (isset($args['page']) && $args['page'] !== ''){
            $offset = $args['offset'];
        }

        return DB::exportAll(DB::findAll('users', 'ORDER BY `id` DESC LIMIT ' . $args['limit'] . ' OFFSET ' . $offset));
    }

    public function getCountUsers()
    {
        $result = DB::getRow('SELECT COUNT(`id`) as count FROM `users`');

        return $result['count'];
    }

    /*
     * Отдает информацию о конкретном юзере из БД
     * */

    public function cronHandlerUsers()
    {
        $users = $this->getCronUsers();
        $handler = new HandlerModel();
        //echo '<br/> Статус обработки:'.$handler->handle(27)->getStatus();;

        foreach ($users as $user) {

            echo $user->gk_first_name . ' загружен <br/>';
            echo 'ID пользователя в GKMK:' . $user->id;
            echo '<br/> Статус обработки:'.$handler->handle($user->id)->getStatus();
        }
    }


    /*
     * [CRON] Отправляет пользователей в МойКласс
     * */

    public function getCronUsers()
    {
        return DB::findAll('users', 'status = ?', ['new']);
    }


    /*
     * [CRON] Юзеры готовые к отправке в МойКласс
     * */

    public function handleUserMoyKlass($userId)
    {
        $sys_user = $this->getUserById($userId);
        if (empty($sys_user['id'])) {
            return;
        }
        // Находим пользователя по емейлу
        $mk_user = self::getFindUserByEmail($sys_user['gk_email']);
        var_dump($mk_user);

        // Если пользователь в МК не найден, создаем нового
        if (empty($mk_user) or $mk_user == null) {

            $dataCreate['name'] = $sys_user['gk_first_name'] . ' ' . $sys_user['gk_last_name'];

            if (!empty($sys_user['gk_email'])) {
                $dataCreate['email'] = $sys_user['gk_email'];
            }

            if (!empty($sys_user['gk_phone'])) {
                $dataCreate['phone'] = $sys_user['gk_phone'];
            }

            $mk_user = MoyklassModel::createUser($dataCreate);
            $status = 'usercreate';
            $status_result[] = 'Юзер в MoyKlass создан.';
        }
        // echo $sys_user['gk_phone'];
        //var_dump($mk_user);
        // return;


        // Находим абонемент который подходит к офферу
        // $subscriptionId = self::getSubscriptionBySumm($sys_user['gk_cost_money']);
        $subscription = self::findSubscriptionByOffer($sys_user['gk_offers']);
        $summa = $sys_user['gk_cost_money'];
        if (!empty($subscription)) {
            if ($summa < $subscription['price']) {
                $summa = $subscription['price'];
            }
        }


        // Начисляем деньги на счет
        $result = MoyklassModel::createPaymentUser([
            'userId' => $mk_user['id'],
            'date' => '' . date("Y-m-d", time()) . '',
            'summa' => intval($summa),
            'optype' => 'income',
            'paymentTypeId' => 1,
            'comment' => 'Тестовое зачисление'
        ]);

        $status = 'success';
        $status_result[] = 'Средства зачислены на счет.';

        // Проверяем, есть ли у клиента в МойКласс участие в группах
        if (!empty($mk_user['joins']) and !empty($mk_user['joins'][0]['classId'])) {

            $classId = $mk_user['joins'][0]['classId'];


            if (!empty($subscription)) {

                // Создаем абонемент для клиента в МК
                $result_subscription = MoyklassModel::createUserSubscriptions([
                    'userId' => $mk_user['id'],
                    'subscriptionId' => $subscription['id'], //24747,
                    'sellDate' => '' . date("Y-m-d", time()) . '',
                    'beginDate' => '' . date("Y-m-d", time()) . '', // Дата начала действия абонемента
                    'classIds' => [$classId],
                    'mainClassId' => $classId,

                ]);
                $status_result[] = 'Абонемент создан.';


                // Находим счет который автоматически был создан после создания абонемента
                $invoiceId = self::getInvoiceByUserSubscription($mk_user['id'], $result_subscription['id']);

                // Делаем списание по счету за абонемент
                $result = MoyklassModel::createPaymentUser([
                    'userId' => $mk_user['id'],
                    'date' => '' . date("Y-m-d", time()) . '',
                    'summa' => -$summa,
                    'optype' => 'debit',
                    'paymentTypeId' => 1,
                    'invoiceId' => $invoiceId,
                    'comment' => 'Тестовое списание за абонемент'
                ]);

                $status = 'success';
                $status_result[] = 'Средства списаны со счета.';

                echo 'Чувак есть в группах. Выдаем абонемент!';
            } else { /// Абонемент не найден по офферу
                $status = 'error';
                $status_result[] = 'Не найдено соответсвие абонемента и оффера.';
            }
        }

        echo 'User found: ' . $mk_user['id'];
        self::setStatusUser($sys_user['id'], [
            'status' => $status,
            'status_result' => $status_result
        ]);
    }


    /*
     * Метод создает абонемент для пользователя
     * Если $userIdMoyKlass пустой - тогда создает нового пользователя
     * */
    /*    public function createAbonement($userIdMoyKlass, $abonementId, $dataUser = ['name', 'email'])
        {
            // Если пользователя нет, создаем его
            if (empty($userIdMoyKlass)) {
                MoyklassModel::createUser($dataUser);
            }
            // Создаем абонемент
            return MoyklassModel::createUserSubscriptions(['userId'=>$userIdMoyKlass, 'subscriptionId'=>$abonementId, 'sellDate'=>''.date("Y-m-d", time()).'']);

        }*/

    public function getUserById($userId)
    {
        return DB::load('users', $userId)->export();
    }

    public function getUserByGkUid($gk_uid)
    {
        return DB::getRow('SELECT * FROM `users` WHERE `gk_uid` = :uid ORDER BY `id` DESC LIMIT 1', ['uid' => $gk_uid]);
    }

    public function getUserByEmail($userEmail)
    {
        return DB::getAll('SELECT * FROM `users` WHERE `gk_email` = :email ORDER BY `id` DESC LIMIT 1', ['email' => $userEmail]);
    }

    public static function getFindUserByEmail($email)
    {
        $mk_user = MoyklassModel::getFindUsers(['email' => $email, 'includeJoins' => 'true']);
        // Если поиск по юзерам вернул больше 1 значения, тогда ищем первое наиболее подходящее

        if (!empty($mk_user['users']) and count($mk_user['users']) > 1) {

            foreach ($mk_user['users'] as $user) {
                if ($user['email'] == $email) {
                    return $user;
                }
            }
            $mk_user = NULL; // Пользователь не найден с указанным email
        } else {
            $mk_user = @$mk_user['users'][0];
        }
        return $mk_user;
    }


    public function findSubscriptionByOffer($offer_id)
    {
        $SyncModel = new SyncModel();
        $res = $SyncModel->getSync(['gk_offer' => $offer_id]);
        $subscription = MoyklassModel::getSubscription($res[0]['mk_sub']);
        if (empty($subscription) or empty($subscription['id'])) {
            return 0;
        }
        return $subscription;
    }

    /*
     * Обрабатывает заявку пользователя (берет юзера из бд и создает ему либо платеж, либо платеж+абонемент если у него уже есть группа)
     * */

    /**
     * Находит счет пользователя по абонементу
     * */
    public static function getInvoiceByUserSubscription($user_id, $subscription_id)
    {
        $user_invoices = MoyklassModel::getInvoices(['userId' => $user_id]);
        $userSubscriptionId = $subscription_id;//; // id созданного абонемента для поиска счета
        $invoiceId = null;
        foreach ($user_invoices['invoices'] as $invoice) {
            if ($invoice['userSubscriptionId'] == $userSubscriptionId) {
                $invoiceId = $invoice['id'];
                break;
            }
        }
        return $invoiceId;
    }

    public static function setStatusUser($userId, $data = ['status', 'status_result'])
    {
        // var_dump($data);
        $user = DB::load('users', $userId);
        if (!empty($data['status']))
            $user->status = $data['status'];

        if (!empty($data['status_result']) and is_array($data['status_result'])) {
            $status_result = '';
            for ($i = 0; $i < count($data['status_result']); $i++) {
                $status_result .= $data['status_result'][$i] . " \n";
            }

            $user->status_result .= $status_result;
        }

        $user->date_update = time();

        return DB::store($user);
    }


    public function setUser($user_id, $data = []){
        $user = DB::load('users', $user_id);
        foreach($data as $key => $value){
            $user->{$key} = $value;
        }
        return DB::store($user);
    }
    /**
     * Функция добавления логов для юзера
     * */
    public function addLogUser($userId, $code, $text = '', $debug = '')
    {
        $user = DB::load('users', $userId);
        $log = DB::dispense('log');
        $log->code = $code;
        $log->text = $text;
        $log->debug = $debug;
        // Добавляем связь с пользователем
        $user->ownLogList[] = $log;
        DB::store($user);
    }

    /**
     * Загрузка логов конкретного юзера
     * */
    public function getLogUser($userId)
    {
        $user = DB::load('users', $userId);
            return DB::exportAll($user->ownLog);
    }
}