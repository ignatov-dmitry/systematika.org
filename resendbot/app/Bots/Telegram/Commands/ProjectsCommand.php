<?php 

namespace Longman\TelegramBot\Commands\UserCommands;

use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use App\Models\Bot\BotProjects;
use App\Models\Bot\BotGenericArg;


class ProjectsCommand extends UserCommand
{
	
	protected $name = 'Все проекты';                      // Your command's name
    protected $description = 'Список всех проектов'; // Your command description
    protected $usage = '/projects';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command
	
	public function execute()
    {
        $message = !empty($this->getMessage())  ? $this->getMessage() : $this->getCallbackQuery()->getMessage();           // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID
		$user_id = $message->getFrom()->getId();

        $bot_trainings = new BotProjects($chat_id, $user_id);
        $bot_trainings->message = $message;
        $bot_trainings->showProjects();

    }
}