<?php

namespace App\Models\Bot;

use \App\Config;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Entities\Keyboard;
use Longman\TelegramBot\Entities\KeyboardButton;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Conversation;

class BotMenu extends SystemCommand
{
    // Содержит в себе массив с менюшками
    private $dataMenu;

    public function __construct()
    {
        Config::init();
        $this->DB = new \App\Lib\DB();
        $this->init();
    }


    public function init()
    {


    }

    # Метод отдаем массив с менюшкой для бота по идентификатору
    public function getMenuByCode($menu_code)
    {
        $menu = $this->DB->query("SELECT * FROM `menu` WHERE `menu_code`=? LIMIT 1", array($menu_code))->fetch(\PDO::FETCH_ASSOC);
        $menu_items = $this->getItemsMenu($menu['id']);
        $menu['items'] = $menu_items;
        return $menu;
    }

    # Метод отдает массив с пунктами для меню
    public function getItemsMenu($menu_id)
    {
        return $this->DB->query("SELECT * FROM `menu_items` WHERE `menu_id`=?", array($menu_id))->fetchAll(\PDO::FETCH_ASSOC);
    }
    # Метод подготавливает массив пунктов меню для вывода в бота


    # Метод сохраняет на каком меню сейчас человек сохранен


    # Метод вывода меню для юзера
    public function showMenuUser($message, $menu_code)
    {

        $chat_id = $message->getChat()->getId();   // Get the current Chat ID

        $build_menu = $this->buildMenu($menu_code);

        $data = [                                  // Set up the new message data
            'chat_id' => $chat_id,                 // Set Chat ID to send the message to
            'text' => 'Меню', // Set message to send
            'reply_markup' => $build_menu
        ];
        $user_id = $message->getFrom()->getId();
        $menu_id = $this->getMenuByCode($menu_code)['id'];
        $this->setCurrentMenuUser($menu_id, $user_id);

        return Request::sendMessage($data);        // Send message!
    }

    function emojiCode($src = '')
    {
        $replaced = preg_replace("/\\\\u([0-9A-F]{1,4})/i", "&#x$1;", $src);
        $result = mb_convert_encoding($replaced, "UTF-16", "HTML-ENTITIES");
        $result = mb_convert_encoding($result, 'utf-8', 'utf-16');
        return $result;
    }

    # Метод конструирует объект с данными из меню
    public function buildMenu($menu_code)
    {

        $menu = $this->getMenuByCode($menu_code);

        // Считаем сколько рядов в Меню
        $menu_rows = $this->countRowsItemsMenu($menu['id']);

        // Пунктов в меню НЕТ. Ничего не показываем
        if ($menu_rows < 1) return Request::emptyResponse();

        // Создаем массив для пустых рядов
        for ($i = 0; $i <= $menu_rows; $i++) {
            $buttons[$i] = [' '];
        }

        // Заполняем ряды кнопками и столбцами
        for ($i = 0; $i <= count($menu['items']); $i++) {
            @$txt = @$menu['items'][$i]['item_name'];
            @$buttons[$menu['items'][$i]['row']][$menu['items'][$i]['col']] = ['text' => $txt];
        }

        // Строим ряды в менюшке (такая реализация, потому что КЛАСС не принимает нормально данные в одном массиве)
        /// Максимум 10
        switch ($menu_rows) {
            default:
            case 1:
                $result_menu = new Keyboard($buttons[0]);
                break;
            case 2:
                $result_menu = new Keyboard($buttons[0], $buttons[1]);
                break;
            case 3:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2]);
                break;
            case 4:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3]);
                break;
            case 5:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3], $buttons[4]);
                break;
            case 6:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3], $buttons[4],
                    $buttons[5]);
                break;
            case 7:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3], $buttons[4],
                    $buttons[5], $buttons[6]);
                break;
            case 8:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3], $buttons[4],
                    $buttons[5], $buttons[6], $buttons[7]);
                break;
            case 9:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3], $buttons[4],
                    $buttons[5], $buttons[6], $buttons[7], $buttons[8]);
                break;
            case 10:
                $result_menu = new Keyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3], $buttons[4],
                    $buttons[5], $buttons[6], $buttons[7], $buttons[8], $buttons[9]);
                break;
        }


        // Делам пересборку по размерам
        if ($menu['resize_menu'] == 1) {
            $result_menu->setResizeKeyboard(true);
        }
        // Скрываем меню после нажатия, если это указано в настройках
        if ($menu['hide_menu_on_click'] == 1) {
            $result_menu->setOneTimeKeyboard(true);
        }

        return $result_menu;
    }

    # Получение клавиатуры от меню
    public function getKeyboardMenu($menu_code)
    {

        $menu = $this->getMenuByCode($menu_code);

        for ($i = 0; $i < count($menu['items']); $i++) {
            $keyboard_items[] = ['text' => $menu['items'][$i]['item_name']];
        }

        $keyboards = new Keyboard($keyboard_items);

        return $keyboards;

    }

    # Считает сколько всего строк в меню
    private function countRowsItemsMenu($menu_id)
    {
        $sql = $this->DB->query("SELECT `id` FROM `menu_items` WHERE `menu_id`=? GROUP by `row`", array($menu_id))->fetchAll(\PDO::FETCH_ASSOC);
        return count($sql);
    }

    # Считает сколько столбцов в конкретной строке меню
    private function countColsItemsMenu($menu_id, $row)
    {
        $sql = $this->DB->query("SELECT `id` FROM `menu_items` WHERE `menu_id`=? && `row`=? GROUP by `col`", array($menu_id, $row))->fetchAll(\PDO::FETCH_ASSOC);
        return count($sql);
    }

    # Метод сохраняет текущее меню пользователя
    ## Нужно вызывать каждый раз, когда показываем пользователю меню
    public function setCurrentMenuUser($menu_id, $user_id)
    {
        $actual_menu = $this->getCurrentMenuUserId($user_id);
        if (empty($actual_menu)) {
            return $this->DB->query("INSERT INTO `menu_actual_user` (`menu_id`, `user_id`) values (?, ?)", array($menu_id, $user_id));
        } else {
            return $this->DB->query("UPDATE `menu_actual_user` SET `menu_id`=? WHERE `user_id`=? LIMIT 1", array($menu_id, $user_id));
        }
    }

    # Метод сохраняет текущее меню пользователя по коду меню
    ## Нужно вызывать каждый раз, когда показываем пользователю меню
    public function setCurrentMenuUserByModeCode($menu_code, $user_id)
    {
        $menu = $this->getMenuByCode($menu_code);
        $this->setCurrentMenuUser($menu['id'], $user_id);
    }

    # Метод возвращает ID текущего меню у пользователя (последнее меню которые было ему показано)
    public function getCurrentMenuUserId($user_id)
    {
        $sql = $this->DB->query("SELECT `menu_id` FROM `menu_actual_user` WHERE `user_id`=? LIMIT 1", array($user_id))->fetch(\PDO::FETCH_ASSOC);
        return $sql['menu_id'];
    }

    # Метод вызывает действие для пункта меню (если пункт найден)
    public function callActionItemMenuUser($message, $user_id, $obj)
    {
        $menu_id = $this->getCurrentMenuUserId($user_id);
        if (!empty($menu_id)) {
            $item_menu = $message->getText();
            $find_item = $this->findItemMenuId($item_menu, $menu_id);
            if (!empty($find_item)) {

                // Пункт в меню найден. Вызываем действие
                $this->callAction($find_item['action'], $find_item['action_data'], $obj, $message);

                // Удаляем сообщение, чтобы было красиво и не засорять чат
                Request::deleteMessage([
                    'chat_id' => $message->getChat()->getId(),
                    'message_id' => $message->getMessageId(),
                ]);

            }
        }
    }

    # Метод ищет пункт меню для конкретной менюшки
    public function findItemMenuId($item_menu, $menu_id)
    {
        return $this->DB->query("SELECT * FROM `menu_items` WHERE `item_name`=? && `menu_id`=?", array($item_menu, $menu_id))->fetch(\PDO::FETCH_ASSOC);
    }

    # Метод выполянет зарегистрированные действия для кнопок меню
    private function callAction($action, $action_data, $obj, $message)
    {
        switch ($action) {
            default:
            case 'command':
                $obj->executeCommand($action_data);
                break;
            case'menu':
                $this->showMenuUser($message, $action_data);
                break;

        }
    }
}
