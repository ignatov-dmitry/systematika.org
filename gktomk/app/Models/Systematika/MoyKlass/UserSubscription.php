<?php


namespace GKTOMK\Models\Systematika\MoyKlass;


use GKTOMK\Models\Systematika\Model;
use GKTOMK\Models\Systematika\Sync;

class UserSubscription extends Model
{
    const INACTIVE = 1;
    const ACTIVE   = 2;
    const FROZEN   = 3;
    const FINISHED = 4;

    protected string $tableName = 'mk_user_subscriptions';

    public function getUserSubscriptionsFromId($id): array
    {
        $countUserSubscriptions = array();
        $individualSyncIds = array();
        $totalVisit = 0;
        $totalVisited = 0;

        $individualSync = (new Sync())->getItems(['individual' => 1], ['mk_sub']);
        array_map(function ($val) use (&$individualSyncIds){
            $individualSyncIds[] = $val;
        }, array_column($individualSync, 'mk_sub'));

        $userSubscriptions = $this->getItems(['userId' => $id, 'statusId' => self::ACTIVE]);

        foreach ($userSubscriptions as $userSubscription){
            if(in_array($userSubscription['subscriptionId'], $individualSyncIds)){
                $countUserSubscriptions['individual']['itemCount'] = $countUserSubscriptions['individual']['itemCount'] + 1;
                $countUserSubscriptions['individual']['visitCount'] = $countUserSubscriptions['individual']['visitCount'] + $userSubscription['visitCount'];
                $countUserSubscriptions['individual']['visitedCount'] = $countUserSubscriptions['individual']['visitedCount'] + $userSubscription['visitedCount'];

            }
            else {
                $countUserSubscriptions['group']['itemCount'] = $countUserSubscriptions['group']['itemCount'] + 1;
                $countUserSubscriptions['group']['visitCount'] = $countUserSubscriptions['group']['visitCount'] + $userSubscription['visitCount'];
                $countUserSubscriptions['group']['visitedCount'] = $countUserSubscriptions['group']['visitedCount'] + $userSubscription['visitedCount'];

            }
            $totalVisit += $userSubscription['visitCount'];
            $totalVisited += $userSubscription['visitedCount'];
        }

        $countUserSubscriptions['all']['itemCount'] = count($userSubscriptions);
        $countUserSubscriptions['all']['visitCount'] = $totalVisit;
        $countUserSubscriptions['all']['visitedCount'] = $totalVisited;


        return $countUserSubscriptions;
    }

    public function getUserSubscriptionsFromEmail($email): array
    {
        $userId = (new User())->getItem(['email' => $email], ['id'])['id'];
        return $this->getUserSubscriptionsFromId($userId);
    }

    public function prepareForGK($email): array
    {
        $subscriptions = array();
        $userId = (new User())->getItem(['email' => $email], ['id'])['id'];
        $userSubscriptions = $this->getUserSubscriptionsFromId($userId);

        $subscriptions['count_user_subscriptions'] = $userSubscriptions['all']['itemCount'] ?: 0;
        $subscriptions['user_subscriptions_left_visits'] = ($userSubscriptions['all']['visitCount'] - $userSubscriptions['all']['visitedCount']) ?: 0;
        $subscriptions['user_subscriptions_left_visits_individual'] = ($userSubscriptions['individual']['visitCount'] - $userSubscriptions['individual']['visitedCount']) ?: 0;
        $subscriptions['user_subscriptions_left_visits_group'] = ($userSubscriptions['group']['visitCount'] - $userSubscriptions['group']['visitedCount']) ?: 0;

        return $subscriptions;
    }
}