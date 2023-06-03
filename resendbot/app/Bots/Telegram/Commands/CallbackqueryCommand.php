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

use App\Models\Bot\BotProjects;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Request;

/**
 * Callback query command
 *
 * This command handles all callback queries sent via inline keyboard buttons.
 *
 * @see InlinekeyboardCommand.php
 */
class CallbackqueryCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'callbackquery';

    /**
     * @var string
     */
    protected $description = 'Reply to callback query';

    /**
     * @var string
     */
    protected $version = '1.1.1';

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */

    public function writeToLog($data, $title = '', $logFile = 'log')
    {
        $log = "\n------------------------\n";
        $log .= date("Y.m.d G:i:s") . "\n";
        $log .= (strlen($title) > 0 ? $title : 'DEBUG') . "\n";
        $log .= var_dump($data, 1);


        $log .= "\n------------------------\n";
        file_put_contents(__DIR__ . '/../logs/' . $logFile . '.log', $log, FILE_APPEND);
        return true;
    }


    public function execute()
    {

        $callback_query = $this->getCallbackQuery();
        $callback_query_id = $callback_query->getId();
        $callback_data = $callback_query->getData();

        // print_r($callback_query);

        $this->message = $callback_query->getMessage();

        //You can use $command as param
        $this->chat_id = $this->message->getChat()->getId();
        $this->user_id = $callback_query->getFrom()->getId();
        //print_r($this->user_id);

        $data = [
            'callback_query_id' => $callback_query_id,
            'text' => 'Загрузка...',//.print_r($this->user_id, 1),
            'show_alert' => $callback_data === 'thumb up',
            'cache_time' => 1,

        ];


        $this->handleQuery($callback_data);

        if (!empty($callback_data))
            //	$this->telegram->executeCommand($callback_data);


            //return Request::editMessageReplyMarkup($data2);

            return Request::answerCallbackQuery($data);
        //return Request::emptyResponse();
    }

    #Метод обрабатывает запросы от инлайн-клавиатуры
    public function handleQuery($callback_data)
    {

        // Преобразуем json в массив (command, method, data)
        $data = explode(" ", $callback_data);

        switch ($data[0]) {

            case 'projects':
                $bot_projects = new BotProjects($this->chat_id, $this->user_id); // Передаем ID чата
                $bot_projects->message = $this->message;
                $bot_projects->callCommand($data);
                break;

        }

    }
}
