<?php


namespace App\Models;

use App\Lib\DBN;
use App\Models\Bitrix\CRest;


class ProjectsModel
{
    public function __construct()
    {
        DBN::init();
    }

    public function getAllTasks($data = []){

        return CRest::call('tasks.task.list', $data);

    }

    public function getAllProjects(){
        $tasks = $this->getAllTasks();

        if(!empty($tasks['result']['tasks']))
            $tasks = $tasks['result']['tasks'];
        else
            return 0;

        $projects = [];
        foreach($tasks as $task){
            if(!empty($task['group'])){
                $projects[] = $task['group'];
            }
        }

        return $projects;
    }

    public function setProjectFavorite($user_id, $project_id){

        $findFav = $this->checkProjectFavorite($user_id, $project_id);
        if(!empty($findFav) and !empty($findFav[0]['id'])){
            return 1;
        }
        $favorite = DBN::dispense('favorite');
        $favorite->user = $user_id;
        $favorite->project = $project_id;
        return DBN::store($favorite);
    }

    public function deleteProjectFavorite($user_id, $project_id){
        $favorite = DBN::find('favorite', 'user = ? && project = ?', [$user_id, $project_id]);
        DBN::trashAll($favorite);
    }

    public function checkProjectFavorite($user_id, $project_id){
        $favorite = DBN::find('favorite', 'user = ? && project = ?', [$user_id, $project_id]);
        return DBN::exportAll($favorite);
    }

    public function getFavoriteUser($user_id){
        $find = DBN::find('favorite', 'user = ?', [$user_id]);
        return DBN::exportALl($find);
    }





}