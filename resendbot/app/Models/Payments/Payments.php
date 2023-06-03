<?php 
// Модель платежей

namespace App\Models\Payments;

class Payments 
{
	
	public function __construct()
	{
		$this->DB = new \App\Lib\DB();
	}
	
	// Получает настройки платежной системы
	public function getSettingPaymentSystems()
	{
		
	}
	
	// Создает новый платеж
	// return int id
	public function createPayment($sum, $user_id)
	{
		$this->DB->query("INSERT INTO `payments` (`sum`, `user_id`, `date_create`) values (?, ?, NOW())", array($sum, $user_id));
		return $this->DB->lastInsertId();
	}
	
	// Получает платеж по ID
	// retrun array
	public function getPaymentById($payment_id)
	{
		return $this->DB->query("SELECT * FROM `payments` WHERE `id`=? LIMIT 1", array($payment_id))->fetch(\PDO::FETCH_ASSOC);
	}
	
	// Устанавливает новый статус для платежа
	public function setStatusPayment($payment_id, $status)
	{
		return $this->DB->query("UPDATE `payments` SET `status`=? WHERE `id`=? LIMIT 1", array($status, $payment_id));
	}
	
	// Завершает заказ с указанным результатом
	public function complatePayment($payment_id, $status)
	{
		
	}
}