<?php 

namespace App\Views;


class PaymentsView extends \App\Lib\Views {
	
	protected $controller;
	protected $model;
	
	
	function __construct(){
		
	}
	
	
	function MainView($data=''){
		if(!empty($data['auth'])){
			echo 'Привет, '.$data['userdata']['first_name'].'!<br/>Ты успешно авторизовался!';
		}else{
			echo 'Привет!<br/>Для авторизации перейдите по ссылке: <a href="tg://resolve?domain=tged_bot&start=auth_'.$data['session_id'].'">Авторизация</a>';
		}
		
		
		echo 'Производим оплату' . print_r($_REQUEST, 1);
		
		
	}
	
	
	
	
	
}