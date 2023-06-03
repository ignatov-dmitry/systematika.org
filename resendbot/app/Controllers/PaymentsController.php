<?php 

namespace App\Controllers;
use \App\Lib\Controller;


use \App\Views\PaymentsView;

class PaymentsController extends Controller {
	
	public $View = '';
	
	
	function __construct(){
		$this->View =  new \App\Views\PaymentsView();
	}
	
	function main(int $payment_id, string $payment_system){
		$User = new \App\Models\User\User;
		$User->sessionInit();
		
		print_r($payment_system);
		$data = ['auth'=>$User->_id, 'session_id'=>$User->sessionId, 'userdata'=>$User->getUser($User->_id)]; 
		
		//print_r($data);
		$this->View->MainView($data);
	}
	
	
	public function getResult(string $payment_system)
  {
    return $payment_system.' result';
  }
	
}