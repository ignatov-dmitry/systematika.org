<?php


namespace GKTOMK\Controllers;

use GKTOMK\Models\CronModel;
use GKTOMK\Models\HandlerHwkModel;
use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\MissingTrialModel;
use GKTOMK\Models\MoyklassModel;
use GKTOMK\Models\StatisticsModel;

class CronController
{

    public function getManual($task = 'string')
    {
        $CronModel = new CronModel();
        return $CronModel->setCronByTask($task);
    }

    public function main()
    {
        //$LeadsModel = new LeadsModel(); // Уже не надо, запускается сразу при необходимости
        //$LeadsModel->cronHandlerUsers();


        // Запускам обработку пропусков
        $MissingTrial = new MissingTrialModel();
        $MissingTrial->handleMissings();


        $CronModel = new CronModel();
        $CronModel->startCron();

    }

    /*
     * Метод для обновления пропусков
     * */
    public function getLoadVisits($period = 0){

        $stats = new StatisticsModel();

        switch($period){

            default:
            case'everyhour': // Каждый час обновляем инфу об
                 $datestart = '2020-10-10';
                $dateend = '2020-11-07';
                break;

            case'everyday':

                break;
        }
        $stats->getLoadVisits($datestart, $dateend);

    }

    public function getTest(){

        $mk_user = LeadsModel::getFindUserByEmail('elle775@mail.ru');
        if (empty($mk_user)) {
            print('User is must be create');
        /*
            $dataCreate['name'] = 'Айгуль Васильева';

           
                $dataCreate['email'] ='elle775@mail.ru';



                $dataCreate['phone'] = '79174164917';


            $create_user = MoyklassModel::createUser($dataCreate);
            $status = 'usercreate';
            $status_result[] = 'Юзер в MoyKlass создан.';

            var_dump($create_user);
            var_dump($status_result);
           */

        }
        var_dump($mk_user);
    }
}