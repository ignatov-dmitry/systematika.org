<?php
/**
 * This file is part of the TelegramBot package.
 *
 * (c) Avtandil Kikabidze aka LONGMAN <akalongman@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Longman\TelegramBot\Commands\SystemCommands;

use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;
use App\Models\Bot\BotGenericArg;

/**
 * Generic command
 *
 * Gets executed for generic commands, when no other appropriate one is found.
 */
class GenericCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'generic';

    /**
     * @var string
     */
    protected $description = 'Handles generic commands or is executed by default when a command is not found';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
	 
	 	 
	 	public function writeToLog($data, $title = '', $logFile='log') {
		   $log = "\n------------------------\n";
		   $log .= date("Y.m.d G:i:s") . "\n";
		   $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
		   $log .= var_dump($data, 1);
		  
			
		   $log .= "\n------------------------\n";
		   file_put_contents(__DIR__ . '/../logs/'.$logFile.'.log', $log, FILE_APPEND);
		   return true;
		}

	 
    public function execute()
    {
	//	$te = var_dump( $this, 1);
	//	$this->writeToLog($this->getUpdate(), 'obj This'); 
		
        $message = !empty($this->getMessage())  ? $this->getMessage() : $this->getCallbackQuery()->getMessage();

        //You can use $command as param
        $chat_id = $message->getChat()->getId();
        $user_id = $message->getFrom()->getId();
        $command = $message->getCommand();

        //If the user is an admin and the command is in the format "/whoisXYZ", call the /whois command
        if (stripos($command, 'whois') === 0 && $this->telegram->isAdmin($user_id)) {
            return $this->telegram->executeCommand('whois');
        }
		//$te = print_r($message);

		/*// Получаем данные которые нам пришли с кнопки (если они есть)
		$callback_data = $this->getUpdate()->getCallbackQuery()->getData();
		if (!empty($callback_data)) {
			$bot_generic_arg = new BotGenericArg();
			
			$args = $bot_generic_arg->checkCommandArguments($callback_data);
			
			if (!empty($args)) {
				// Сохраняем аргумент
				$bot_generic_arg->setArgument($user_id, $chat_id, $args['command'], $args['argument']);
				// Снова вызываем команду без аргументов (аргументы будут уже в базе данных и команда их сама достанет)
				return $this->telegram->executeCommand($args['command']);
			}
		}*/
		
		
		
		
		
        $data = [
            'chat_id' => $chat_id,
            'text'    => 'Command /' . $command . ' not found.. :(' ,
        ];

        return Request::sendMessage($data);
    }
}
