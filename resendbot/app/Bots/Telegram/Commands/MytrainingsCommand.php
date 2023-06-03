<?php 

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;


class MytrainingsCommand extends UserCommand
{
	
	protected $name = 'Мои тренинги';                      // Your command's name
    protected $description = 'Список доступных тренингов'; // Your command description
    protected $usage = '/mytrainings';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command
	
	public function execute()
    {
		// Если объект сообщений пуст - проверяем объект callbackquery (inline-menu)
        $message = !empty($this->getMessage())  ? $this->getMessage() : $this->getCallbackQuery()->getMessage();            // Get Message object
		$chat_id = $message->getChat()->getId();   // Get the current Chat ID
       

        $data = [                                  // Set up the new message data
            'chat_id' => $chat_id,                 // Set Chat ID to send the message to
            'text'    => 'У вас нет доступных тренингов.', // Set message to send
        ];

        return Request::sendMessage($data);        // Send message!
    }
}