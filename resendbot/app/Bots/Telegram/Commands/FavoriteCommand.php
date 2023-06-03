<?php 

namespace Longman\TelegramBot\Commands\UserCommands;

use App\Models\Bot\BotMenuInline;
use App\Models\ProjectsModel;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Request;
use App\Models\Bot\BotGenericArg;


class FavoriteCommand extends UserCommand
{
	
	protected $name = 'Избранное';                      // Your command's name
    protected $description = 'Список избранных проектов'; // Your command description
    protected $usage = '/favorite';                    // Usage of your command
    protected $version = '1.0.0';                  // Version of your command
    /**
     * @var int
     */
    private $chat_id;
    /**
     * @var int
     */
    private $user_id;

    public function execute()
    {
        $message = !empty($this->getMessage())  ? $this->getMessage() : $this->getCallbackQuery()->getMessage();           // Get Message object
        $this->chat_id = $message->getChat()->getId();   // Get the current Chat ID
        $this->user_id = $message->getFrom()->getId();

        $this->showFavorite();
    }

    public function showFavorite(){
        $ProjectsModel = new ProjectsModel();

        $favs = $ProjectsModel->getFavoriteUser($this->user_id);
        //$favs = print_r($favs, 1);

        if(empty($favs)){
            $text = 'Список избранных проектов пуст.';
        }else{
            $text = 'Ваши избранные проекты:';
            $inline_menu = $this->buildMenuFavorite($favs);
        }


        $data = [
            'chat_id' => $this->chat_id,
            'text' => $text,
            //'reply_markup' => $inline_keyboard
        ];
        if (!empty($inline_menu))
            $data['reply_markup'] = $inline_menu;


        return Request::sendMessage($data);

    }

    private function buildMenuFavorite($favs){

        $num = 0;
        foreach ($favs as $fav) {
            $menu[] = [
               'item_name' => 'Проект ID '.$fav['project'],
                'command' => 'projects show '.$fav['project'],
                'row' => $num,
                'col' => 0
            ];
            $num++;
        }

        $BotMenuInline = new BotMenuInline();
        return $BotMenuInline->buildInlineMenu($menu);

    }
}