<?php

namespace App\Models\Bot;

use App\Models\ProjectsModel;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;


class BotProjects extends ProjectsModel
{
    public $message;
    private $chat_id;
    private $user_id;
    private $telegram;

    public function __construct($chat_id, $user_id)
    {
        parent::__construct();
        $this->chat_id = $chat_id;
        $this->user_id = $user_id;
    }

    # Вызывает команды из inline-меню
    public function callCommand($arg)
    {
        switch ($arg[1]) {
            case 'show':
                $this->showProject($arg[2]);
                break;

            case 'favor':
                $this->favoriteProject($arg[2]);
                break;

            default:
                $this->showProjects();
                break;
        }

    }

    # Создает и выводит список всех тренингов для бота

    public function showProject($project_id)
    {
        // $training = $this->getTraining($training_id);


        $me = print_r($me, 1);
        $text = 'Меню проекта ID ' . $project_id . PHP_EOL . 'Выберите действие:' . $me;
        // $text .= $training['description'];

        $buttons['buy'][] = ['text' => 'Купить', 'callback_data' => 'trainings buy ' . $training_id];
        $buttons['back'][] = ['text' => 'Назад', 'callback_data' => 'trainings main 1'];

        $check = $this->checkProjectFavorite($this->user_id, $project_id);
        if (!empty($check[0]['id'])) { // Проект уже в избранном
            $favorite_button = 'Удалить из избранного';
        } else {
            $favorite_button = 'В избранное';
        }

        $menu_rows = [
            ['item_name' => $favorite_button, 'command' => 'projects favor ' . $project_id, 'row' => 0, 'col' => 0],
            ['item_name' => 'О проекте', 'command' => 'projects about ' . $project_id, 'row' => 1, 'col' => 0],
            ['item_name' => 'Участники', 'command' => 'projects workers ' . $project_id, 'row' => 2, 'col' => 0],
            ['item_name' => 'Задачи проекта', 'command' => 'projects tasks ' . $project_id, 'row' => 3, 'col' => 0],
            ['item_name' => 'Все проекты', 'command' => 'projects main', 'row' => 4, 'col' => 0]
        ];

        $BotMenuInline = new BotMenuInline();
        $inline_keyboard = $BotMenuInline->buildInlineMenu($menu_rows);
        /*
        $data = [
            'chat_id' => $this->chat_id,
            'text'    => $text . print_r( $this->message->raw_data['message_id'], 1),
            'reply_markup' => $inline_keyboard
        ];

        return Request::sendMessage($data);
        */

        $dataEdit = [
            'chat_id' => $this->chat_id,
            'message_id' => $this->message->raw_data['message_id'],
            'text' => $text,
            'reply_markup' => $inline_keyboard
        ];


        return Request::editMessageText($dataEdit);
    }

    /*
     * Формирует меню для списка проектов с кнопками навигации
     * */

    public function favoriteProject($project_id)
    {

        $check = $this->checkProjectFavorite($this->user_id, $project_id);
        if (!empty($check[0]['id'])) {
            // $text = 'Проект удален из избранного!';
            $this->deleteProjectFavorite($this->user_id, $project_id);
        } else {
            // $text = 'Проект успешно добавлен в избранное!';
            $this->setProjectFavorite($this->user_id, $project_id);
        }

        $this->showProject($project_id);
    }


    public function showProjects()
    {

        $all_projects = $this->getAllProjects();


        // $text = print_r($all_projects, 1);


        if (count($all_projects) == 0) {
            $text = "Проектов нет!" . PHP_EOL;
        } else {
            $text = "Список проектов:" . PHP_EOL;
            $inline_menu = $this->buildMenuAllProjects();
        }

        //  $me = Request::getMe();
        //  $me = print_r($this->user_id);
        $data = [
            'chat_id' => $this->chat_id,
            'text' => $text
        ];

        if (!empty($inline_menu))
            $data['reply_markup'] = $inline_menu;


        if (!empty($this->message) and !empty($arg[0])) {
            $data['message_id'] = $this->message->raw_data['message_id'];
            return Request::editMessageText($data);
        } else
            return Request::sendMessage($data);
    }



    # Вывод менюшки действий по конкретному проекту

    public function buildMenuAllProjects($page = 0)
    {

        $BotMenuInline = new BotMenuInline();

        $all_projects = $this->getAllProjects();

        if (count($all_projects) > 0) {
            // Составляем список проектов
            $menu_rows = [];
            $num = 0;
            foreach ($all_projects as $proj) {
                $menu_rows[] = [
                    'item_name' => $proj['name'],
                    'command' => 'projects show ' . $proj['id'],
                    'row' => $num,
                    'col' => 0,
                ];
                $num++;

            }


            return $BotMenuInline->buildInlineMenu($menu_rows);;
        } else {
            return 0;
        }

    }



}