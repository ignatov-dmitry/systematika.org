<?php 

namespace App\Lib;

//protected $output;

class Views {
	
	public $views = [];
	
    public function __construct() {
		return $this;
    }
	// Вызываем ПРЕДСТАВЛЕНИЯ которые были указаны через setView
	function output(){
		
		for($i=0;$i<count($this->views);$i++){
			$obj =  new $this->views[$i]['class']();
			
			$method = $this->views[$i]['method'];
			$obj->$method($this->views[$i]['data']);
		}
	}
	// Метод принимает какие представления нужно будет вызвать
	public function setView($view, $data){
		//use Edtgbot\Views;
		$class = '\\App\\Views\\'.$view.'View';
		$method = $view.'View';
		
		$this->views[] = ['class'=>$class, 'method'=>$method, 'classname'=>$view, 'data'=>$data];
		
	}
	
	
}