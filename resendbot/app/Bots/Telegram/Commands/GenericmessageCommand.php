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

use App\Models\Bot\BotMenu;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;
use Longman\TelegramBot\Request;

/**
 * Generic message command
 *
 * Gets executed when any type of message is sent.
 */
class GenericmessageCommand extends SystemCommand
{
    /**
     * @var string
     */
    protected $name = 'genericmessage';

    /**
     * @var string
     */
    protected $description = 'Handle generic message';

    /**
     * @var string
     */
    protected $version = '1.1.0';

    /**
     * @var bool
     */
    protected $need_mysql = true;

    /**
     * Command execute method if MySQL is required but not available
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function executeNoDb()
    {
        // Do nothing
        return Request::emptyResponse();
    }

    /**
     * Command execute method
     *
     * @return \Longman\TelegramBot\Entities\ServerResponse
     * @throws \Longman\TelegramBot\Exception\TelegramException
     */
    public function execute()
    {
        //Если разговор занят, выполните команду разговора после обработки сообщения
        $conversation = new Conversation(
            $this->getMessage()->getFrom()->getId(),
            $this->getMessage()->getChat()->getId()
        );

        //Извлечь команду разговора, если она существует, и выполнить ее
        if ($conversation->exists() && ($command = $conversation->getCommand())) {
            return $this->telegram->executeCommand($command);
        }

        $bot_menu = new BotMenu();
        //$test_menu = print_r( $bot_menu->getMenuByCode('start_menu') , 1);

        //$items_menu = print_r($bot_menu->getItemsMenu($test_menu['id']), 1);

        $message = $this->getMessage();            // Get Message object
        $chat_id = $message->getChat()->getId();   // Get the current Chat ID

        //$menu_keyboard = $bot_menu->getKeyboardMenu('start_menu');
        //$positions = $bot_menu->getItemsPosition('start_menu');
        $test = print_r($message->getText(), 1);

        $data = [                                  // Set up the new message data
            'chat_id' => $chat_id,                 // Set Chat ID to send the message to
            'text' => 'Debug eli' . $test, // Set message to send

        ];

        // Регистрируем команду Меню
        if ('Меню' === $message->getText()) {
            $bot_menu->showMenuUser($message, 'start_menu');
        }

        // Вызываем действие для пункта меню, если оно существует
        $bot_menu->callActionItemMenuUser($message, $message->getFrom()->getId(), $this->telegram);
        //var_dump($bot_menu);



        // Делаем функцию отправки сообщений в группу поддержки
        /// Проверяем, что это приватный чат
        if ($this->getMessage()->getChat()->isPrivateChat()) {


            $mess = trim($message->getText(true));

            $login = $this->getMessage()->getFrom()->getUsername();
            $firstname = $this->getMessage()->getFrom()->getFirstName();
            //$lastname = $this->getMessage()->getFrom()->getFirstName();

            $text = 'Сообщение от ' . $firstname . ' (@' . $login . '):' . PHP_EOL;
            $text .= $mess . PHP_EOL;

            // Отправляем все сообщения боту - в группу поддержки

            $data = [
                'chat_id' => CONFIG['support_group_id'],
                'text' => $text, //. print_r(, 1),
            ];

            return Request::sendMessage($data);
        }


        //$this->telegram->executeCommand('mytrainings');

        // return Request::sendMessage($data);        // Send message!

        //return Request::emptyResponse();
    }
}
