<?php 

namespace App\Models;


class Trainings 
{

	
	public function __construct()
	{
		$this->DB = new \App\Lib\DB();
		
	}
	
	# Отдает список всех активных тренингов
	public function getAllTrainings()
	{
		return $this->DB->query("SELECT * FROM `trainings`")->fetchAll(\PDO::FETCH_ASSOC);
	}
	
	# Метод отдает конкретный тренинг по ID
	public function getTraining($training_id)
	{
		return $this->DB->query("SELECT * FROM `trainings` WHERE `id`=? LIMIT 1", array($training_id))->fetch(\PDO::FETCH_ASSOC);	
	}
	
	
	
	
	
	
	
	
}