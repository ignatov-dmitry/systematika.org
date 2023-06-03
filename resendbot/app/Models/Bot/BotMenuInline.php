<?php 
## Модель управления инлайн-клавиатурой (меню) в боте
namespace App\Models\Bot;


use App\Models\ProjectsModel;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;

use App\Models\Bot\BotTrainings;


class BotMenuInline extends SystemCommand
{

	public function __construct()
	{
		$this->DB = new \App\Lib\DB();
	}
	
	
	## Метод создает меню исходя из указанного массива данных
	public function buildInlineMenu($rows = [])
	{

        /*return new InlineKeyboard([
            ['text' => 'Test button', 'callback_data' => '1'],
            ['text' => 'Test button 2', 'callback_data' => '2']
            ]);*/
	    $num_rows = $this->countRowsMenu($rows);

        // Создаем массив для пустых рядов
        for ($i = 0; $i <= $num_rows; $i++) {
            $buttons[$i] = [
                ['text' => ' ', 'callback_data' => '0']
            ];
        }

        // Заполняем ряды кнопками и столбцами
        for ($i = 0; $i <= $num_rows; $i++) {

            $buttons[$rows[$i]['row']][$rows[$i]['col']] = [
                'text' => $rows[$i]['item_name'],
                'callback_data' => $rows[$i]['command']
            ];
        }

	    switch($num_rows){
            default:
            case 1:
                $menu = new InlineKeyboard($buttons[0]);
                break;

            case 2:
                $menu = new InlineKeyboard($buttons[0], $buttons[1]);
                break;

            case 3:
                $menu = new InlineKeyboard($buttons[0], $buttons[1], $buttons[2]);
                break;

            case 4:
                $menu = new InlineKeyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3]);
                break;

            case 5:
                $menu = new InlineKeyboard($buttons[0], $buttons[1], $buttons[2], $buttons[3], $buttons[4]);
                break;

            case 6:
                $menu = new InlineKeyboard($buttons[0], $buttons[1]);
                break;
        }

        return $menu;
	}


	private function countRowsMenu($rows){
	    $count_rows = [];
	    foreach($rows as $row){
            $count_rows[$row['row']] = 1;
        }
	    return count($count_rows);
    }

	
}