<?php

namespace App\Models;

use App\Lib\DBN;
use \RedBeanPHP\R as R;

class Test
{

    public function __construct()
    {
        DBN::init(); // соединение с бд
    }

    public function init()
    {


        $club = R::dispense('club');
        $club['type'] = 'chat';
        R::store( $club );
    }


}