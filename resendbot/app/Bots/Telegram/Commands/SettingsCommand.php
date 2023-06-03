<?php 

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Models\Bot\BotMenu;
use App\Models\Bot\BotMenuInline;
use App\Models\ProjectsModel;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use App\Models\Bot\BotGenericArg;


class SettingsCommand extends UserCommand
{
	
	protected $name = 'Настройки';                      // Your command's name
    protected $description = 'Настройки'; // Your command description
    protected $usage = '/settings';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command
    /**
     * @var int
     */
    private $chat_id;
    /**
     * @var int
     */
    private $user_id;
    /**
     * @var \Longman\TelegramBot\Entities\Message
     */
    private $message;

    public function execute()
    {
        $this->message = !empty($this->getMessage())  ? $this->getMessage() : $this->getCallbackQuery()->getMessage();           // Get Message object
        $this->chat_id = $this->message->getChat()->getId();   // Get the current Chat ID
        $this->user_id = $this->message->getFrom()->getId();

        $this->showSettings();
    }

    public function showSettings(){

        $bot_menu = new BotMenu();
        return $bot_menu->showMenuUser($this->message, 'settings');

    }
}