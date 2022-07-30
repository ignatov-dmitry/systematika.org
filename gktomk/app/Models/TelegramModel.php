<?php


namespace GKTOMK\Models;


use \Longman\TelegramBot\Entities\InlineKeyboard;
use \Longman\TelegramBot\Request;
use \Longman\TelegramBot\Telegram;

class TelegramModel
{

    public static function sendMessage($chat_id, $message, $keyboard = [])
    {
        $telegram = new Telegram(CONFIG['bot_api_key'], CONFIG['bot_username']);

        $data = [
            'chat_id' => $chat_id,
            'text' => $message,
        ];

        if(!empty($keyboard)){
            $data['reply_markup'] = new InlineKeyboard($keyboard);
        }

        $result = Request::sendMessage($data);

        if($result->isOk()){
            return 1;
        }else
            return 0;
    }

}