<?php 
// Модель позволяет временно сохранять аргументы к коммандам.
// executeCommand() не позволять передавать аргементы, эта модель помогает решить эту задачу
namespace App\Models\Bot;

class BotGenericArg 
{
	
	public function __construct()
	{
		$this->DB = new \App\Lib\DB();
	}
	// Сохраняет команду и аргумент для текущего чата и пользователя
	public function setArgument($user_id, $chat_id, $command, $argument)
	{
		// Удаляем старый аргумент. Всегда храним только 1 аргумент для 1 команды
		$this->delArgument($user_id, $chat_id, $command);
		
		return $this->DB->query("
			INSERT INTO `generic_arguments`
			(`user_id`,`chat_id`,`command`,`argument`) 
			values 
			(?, ?, ?, ?)", 
			array($user_id, $chat_id, $command, $argument)
			);
	}
	
	// Возвращает аргумент для команды текущего чата и пользователя
	public function getArgument($user_id, $chat_id, $command, $del=true)
	{
		$sql = $this->DB->query("
			SELECT `argument` 
			FROM `generic_arguments` 
			WHERE `user_id`=? && `chat_id`=? && `command`=? 
			LIMIT 1", 
			array($user_id, $chat_id, $command))->fetch(\PDO::FETCH_ASSOC);
		
		if (!empty($sql['argument'])) {
			$result = $sql['argument'];
		} else { $result = null; }
			
		if ($del==true) {
			// Удаляем старый аргумент. Всегда храним только 1 аргумент для 1 команды
			$this->delArgument($user_id, $chat_id, $command);
		}
		return $this->decodeArg($result);
	}
	
	// Удаляем аргумент
	public function delArgument($user_id, $chat_id, $command)
	{
		return $this->DB->query("
			DELETE FROM `generic_arguments` 
			WHERE `user_id`=? && `chat_id`=? && `command`=? 
			LIMIT 1", 
			array($user_id, $chat_id, $command));
	}
	
	// Проверяет, есть ли в команде аргументы
	public function checkCommandArguments($command)
	{
		$exp = $this->decodeArg($command);
		if(count($exp)>1){
			
			$com = $exp[0];
			unset($exp[0]);
			$arg = trim(str_replace($com, '', $command));
			
			return array('command' => $com, 'argument' => $arg);
		}
		
	}
	// Метод декодирует строку аргумента в массив 
	private function decodeArg($arg)
	{
		$arg = explode(" ", $arg);
		return $arg;
	}
	
}