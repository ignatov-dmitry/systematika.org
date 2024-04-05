<?php


namespace GKTOMK\Models;

/*
 * Для работы с абонементами клиентов
 *
 * */

class SubscriptionsModel
{

    public function getCountSubscriptionsByEmail($email){
        $userMk = MoyklassModel::getUserByEmail($email);

        if(!isset($userMk) or !isset($userMk['email']))
            return 'mk user not found';

        return $this->getCountSubscriptionsByMkUid($userMk['id']);
    }
    /*
     * Отдает кол-во оставшихся индивидуальных абонементов у клиента
     * */
    public function getCountSubscriptionsByMkUid($mk_uid){


        // Получаем количество абонементов у клиента
        //$user_subscriptions = MoyklassModel::getUserSubscriptions(['userId' => $mk_uid, 'statusId' => '2']);
        $user_subscriptions['subscriptions'] = MoyklassModel::getUserSubscriptionsFromDb($mk_uid);

        //print_r($user_subscriptions);

        // Заправшиваем список индивидуальных абонементов
        $SyncModel = new SyncModel();
        $getSyncOnlyMkSubIds = $SyncModel->getSyncOnlyMkSubIds(['individual' => 1]);


        // Фильтруем список / делаем подсчеты
        $countSubscriptions = [];
        foreach ($user_subscriptions['subscriptions'] as $user_subscription){
            if(in_array($user_subscription['subscriptionId'], $getSyncOnlyMkSubIds)){
                $countSubscriptions['individual']['itemCount'] = $countSubscriptions['individual']['itemCount'] + 1;
                $countSubscriptions['individual']['visitCount'] = $countSubscriptions['individual']['visitCount'] + $user_subscription['visitCount'];
                $countSubscriptions['individual']['visitedCount'] = $countSubscriptions['individual']['visitedCount'] + $user_subscription['visitedCount'];
            }
            if(in_array($user_subscription['subscriptionId'], $getSyncOnlyMkSubIds) == false) {
                $countSubscriptions['group']['itemCount'] = (int)@$countSubscriptions['group']['itemCount'] + 1;
                $countSubscriptions['group']['visitCount'] = (int)@$countSubscriptions['group']['visitCount'] + $user_subscription['visitCount'];
                $countSubscriptions['group']['visitedCount'] = (int)@$countSubscriptions['group']['visitedCount'] + $user_subscription['visitedCount'];
            }

            $countSubscriptions['all']['itemCount'] = count($user_subscriptions['subscriptions']);
            $countSubscriptions['all']['visitCount'] += $user_subscription['visitCount'];
            $countSubscriptions['all']['visitedCount'] += $user_subscription['visitedCount'];
        }


        //var_dump($countSubscriptions);die();
        //var_dump($user_subscriptions['stats']);

        return $countSubscriptions;
    }

}
