<?php

namespace App\Models\User;


class User {
	
	public $_id = null;
	public $sessionId = null;
	
	
	protected $sessionName = 'btaskbot';
	protected $DB = '';
	
	function __construct(){
		$this->DB = new \App\Lib\DB;
		
	}
	
	public function sessionInit(){
        session_name($this->sessionName);
		session_start();

		$this->sessionId = session_id();
		if(empty($this->is_session())){
			$this->registerSession();
		}
		$this->is_auth();
	}
	
	// Проверяем, авторизован ли пользователь
	public function is_auth(){
		
		$sql = $this->DB->query("SELECT `user_id` FROM `user_session` `us`, `user` `u` WHERE `session_id`=? && `u`.`id`=`us`.`user_id` LIMIT 1", array($this->sessionId))->fetch();
		$this->_id = @$sql['user_id'];
		return @$sql['user_id'];
	}
	// Проверяет, зарегистрирована ли сессия пользователя в базе
	public function is_session(){
		
		$sql = $this->DB->query("SELECT `id` FROM `user_session` WHERE `session_id`=? LIMIT 1", array($this->sessionId))->fetch();
		return $sql['id'];
	}
	// Метод ищет активную сессию в базе данных по SESSIONID
	public function findSession($sessionId){
		$sql = $this->DB->query("SELECT * FROM `user_session` WHERE `session_id`=? LIMIT 1", array($sessionId))->fetch();
		return $sql;
	}
	// Регистрирует создает пользователя в базе данных
	public function  registerSession(){
		$this->DB->query("INSERT INTO `user_session` (`session_id`) values (?)", array($this->sessionId));	
	}
	// Метод отдает данные о пользователе
	public function getUser($id){
		$sql = $this->DB->query("SELECT * FROM `user` WHERE `id`=? LIMIT 1", array($id))->fetch();
		//var_dump($sql);
		return $sql;
	}
	
	// Привязывает конкретную сессию к пользователю
	public function authSessionUser($sessionId, $userId){
		return $this->DB->query("UPDATE `user_session` SET `user_id`=? WHERE `session_id`=? LIMIT 1", array($userId, $sessionId));
	}
	// Убирает авторизацию пользлвателя и удаляет сессию
	public function logOutUser($userId){
		return $this->DB->query("UPDATE `user_session` SET `user_id`=NULL WHERE `user_id`=? LIMIT 1", [$userId]);
	}
}