<?php 

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use App\Models\Bot\BotTrainings;
use App\Models\Bot\BotGenericArg;


class TrainingsCommand extends UserCommand
{
	
	protected $name = 'Все тренинги';                      // Your command's name
    protected $description = 'Витрина тренингов'; // Your command description
    protected $usage = '/trainings';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command
	
	public function execute()
    {
        $message = !empty($this->getMessage())  ? $this->getMessage() : $this->getCallbackQuery()->getMessage();           // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
		$user_id = $message->getFrom()->getId();
		
		// Смотрим есть ли аргументы для текущей команды
		$bot_arguments = new BotGenericArg();
		$arguments = $bot_arguments->getArgument($user_id, $chat_id, 'trainings', 0);
		
			$bot_trainings = new BotTrainings($chat_id);
			$bot_trainings->message = $message;
			switch ($arguments[0]) {
			
				default:
				case 'main':
					$bot_trainings->showTrainings();
					break;
					
				case 'show':
					$bot_trainings->showTraining($arguments[1]);
					break;
					
				case 'buy':
					$bot_trainings->showBuyTraining($arguments[1]);
					break;
				
				case 'pay':
					$bot_trainings->showPayTraining($arguments[1], $arguments[2]);
					break;
			
			}
			// Удаляем аргумент
			return $bot_arguments->delArgument($user_id, $chat_id, 'trainings');
			/*
			$data = [                                  // Set up the new message data
				'chat_id' => $chat_id,                 // Set Chat ID to send the message to
				'text'    => 'Все супер'.print_r($arguments[0], 1), // Set message to send
			];
			Request::sendMessage($data);   
			
			*/
			
			//
  
			
			

		
		

       // return Request::sendMessage($data);        // Send message!
    }
}