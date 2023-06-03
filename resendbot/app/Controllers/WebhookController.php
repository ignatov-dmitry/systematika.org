<?php

namespace App\Controllers;

use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;

class WebhookController extends Controller
{


    public function main()
    {
        $get = @$_GET;
        $this->writeToLog($get, 'webhook request');

        if(!$get['uid'])
            return 0;

        try {
            $telegram = new Telegram(CONFIG['bot_api_key'], CONFIG['bot_username']);
            $telegram->enableMySql(CONFIG['mysql_credentials']);
        } catch (TelegramException $e) {
        }

        $text = 'Новый запрос в Срочную поддержку!' . PHP_EOL;
        $text .= "Пользователь " . @$get['first_name'] . " " . @$get['last_name']  . PHP_EOL;

        $inline_keyboard = new InlineKeyboard([
            ['text' => 'Перейти', 'url' => CONFIG['gk_url'] . "/user/control/user/update/id/" . @$get['uid']],
        ]);

        $data = [
            'chat_id' => CONFIG['support_group_id'],
            'text' => $text, //. print_r(, 1),
            'reply_markup' => $inline_keyboard,
        ];


            $result = Request::sendMessage($data);

            if($result->isOk()){
                return 1;
            }else
                return 0;
    }
}