<?php
// Модель для работы с платежными системами

namespace \App\Models\Payments;




class PaymentSystems extends Payments
{
	
	
	public function __construct($payment_system)
	{
		switch($payment_system){
			
			case 'megakassa':
				$class = 'MegakassaSystem';
				break;
				
			default:
				return 'not found';
		}
		
		$obj = new '\\Edtgbot\\Models\\Payments\\Systems\\'.$class();
		
		return $obj;
	}
	
	
	
}

