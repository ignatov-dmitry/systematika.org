<?php 

namespace App\Models\Bot;

use App\Models\ProjectsModel;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Commands\SystemCommand;
use Longman\TelegramBot\Commands\UserCommand;
use Longman\TelegramBot\Entities\InlineKeyboard;
use App\Models\Bot\BotGenericArg;


class BotTrainings extends ProjectsModel
{
	protected $chat_id;
	public $message;
		
	public function __construct($chat_id)
	{
		$this->DB = new \App\Lib\DB();
		$this->chat_id = $chat_id;
	}
	# Вызывает команды из inline-меню
	public function callCommand($data, $id) {
		
		switch($data){
			case 'show':
				$this->showTraining($id);
				break;
				
			default: break;
		}
		
	} 
	
	# Создает и выводит список всех тренингов для бота
	public function showProjects()
	{
		$trainings = new ProjectsModel();
		$all_trainings = $trainings->getAllProjects();

		$text = print_r($all_trainings, 1);
		
		$text = "Список тренингов:".PHP_EOL;
		
		for ($i=0; $i<count($all_trainings); $i++) { 

			$buttonsInlineMenu[] = [
				'text' 				=> $all_trainings[$i]['training_name'], 
				'callback_data' 	=> 'trainings show '.$all_trainings[$i]['id'],
			];
		}
		
		$inline_keyboard = new InlineKeyboard($buttonsInlineMenu);
		
		$chat_id = $this->chat_id;
		$user_id = $this->message->from['id'];
		
		$bot_generic_arg = new BotGenericArg();
		$arg = $bot_generic_arg->getArgument($user_id, $chat_id, 'trainings');
		
		$data = [                                  
            'chat_id' => $this->chat_id,                 
            'text'    => $text /*. print_r($arg, 1)*/,
			'reply_markup' => $inline_keyboard
			
        ];

		
		
		if (!empty($this->message) and !empty($arg[0])) {
			$data['message_id'] = $this->message->raw_data['message_id'];
			return Request::editMessageText($data);
		} else 
			
		return Request::sendMessage($data);
	}
	
	# Метод выводит один конкретный тренинг
	public function showTraining($training_id) 
	{
		$training = $this->getTraining($training_id);
		
		$text = 'Тренинг '. $training['training_name'].PHP_EOL;
		$text .= $training['description'];
		
		$buttons['buy'][] = ['text' => 'Купить', 'callback_data' => 'trainings buy '.$training_id];
		$buttons['back'][] = ['text' => 'Назад', 'callback_data' => 'trainings main 1'];
		
		
		
		
		$inline_keyboard = new InlineKeyboard($buttons['buy'], $buttons['back']);
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
            'text'    => $text,
			'reply_markup' => $inline_keyboard
        ];
		
		
		return Request::editMessageText($dataEdit);
	}
	
	# Метод выводит информацию о возможности покупки тренинга
	public function showBuyTraining($training_id)
	{
		$training = $this->getTraining($training_id);
		$text = 'Оформление тренинга '. $training['training_name'].PHP_EOL;
		$text .= '-------'.PHP_EOL;
		$text .= 'Выберите систему оплаты '.PHP_EOL;
		
		
		$buttons['pay'][] = ['text' => 'Мегакасса (комиссия: 2%)', 'callback_data' => 'trainings pay '.$training_id.' megakassa'];
		$buttons['back'][] = ['text' => 'Назад', 'callback_data' => 'trainings show '.$training_id];
		
		
		$inline_keyboard = new InlineKeyboard($buttons['pay'], $buttons['back']);
		
		
		$dataEdit = [                                  
            'chat_id' => $this->chat_id,                 
			'message_id' => $this->message->raw_data['message_id'],
            'text'    => $text,
			'reply_markup' => $inline_keyboard
        ];
		
		
		return Request::editMessageText($dataEdit);
		
	}
	
	# Метод выводит информацию о возможности покупки тренинга
	public function showPayTraining($training_id, $payment_system)
	{
		$training = $this->getTraining($training_id);
		$text = 'Оплата тренинга '. $training['training_name'].PHP_EOL;
		$text .= '-------'.PHP_EOL;
		
		if ($payment_system=='megakassa') {
			$payment_system_name = 'Мегакасса';
		}
		
		
		$text .= 'Вы хотите оплатить тренинг с помощь платежной системы '.$payment_system_name .PHP_EOL;
		$text .= ''.PHP_EOL;
		$text .= 'Сумма к оплате: миллион рублей'.PHP_EOL .PHP_EOL;
		$text .= 'После нажатия кнопки "Оплатить" вы будете переадресованы на страницу платежного агрегатора для оплаты.';
		
		$payment = new \App\Models\Payments\Payments();
		$payment_id = $payment->createPayment('100', 1);
		
		
		$buttons['pay'][] = ['text' => 'Оплатить', 'url' => CONFIG['url_site'].'/payments/'.$payment_id.'/'.$payment_system];
		$buttons['back'][] = ['text' => 'Отмена', 'callback_data' => 'trainings main'];
		
		
		$inline_keyboard = new InlineKeyboard($buttons['pay'], $buttons['back']);
		
		
		$dataEdit = [                                  
            'chat_id' => $this->chat_id,                 
			'message_id' => $this->message->raw_data['message_id'],
            'text'    => $text,
			'reply_markup' => $inline_keyboard
        ];
		
		
		return Request::editMessageText($dataEdit);
	}
}