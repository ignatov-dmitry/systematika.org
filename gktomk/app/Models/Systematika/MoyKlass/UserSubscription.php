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

    public function getUserSubscriptions($id): array
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
}