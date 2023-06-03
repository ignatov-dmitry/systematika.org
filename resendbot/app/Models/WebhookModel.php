<?php


namespace App\Models;


use App\Lib\DBN;

class WebhookModel
{
    /*
     * Создает сообщение для бота
     * */
    public function createMessage($data = []){

        DBN::exec('INSERT INTO `messages` ');
    }

}