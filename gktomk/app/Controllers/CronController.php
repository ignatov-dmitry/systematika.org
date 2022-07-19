<?php


namespace GKTOMK\Controllers;

use GKTOMK\Models\LeadsModel;
use GKTOMK\Models\MoyklassModel;

class CronController
{

    public function main()
    {
        $LeadsModel = new LeadsModel();
        $LeadsModel->cronHandlerUsers();

       //var_dump(  );

        //var_dump(MoyklassModel::getSubscriptions());


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