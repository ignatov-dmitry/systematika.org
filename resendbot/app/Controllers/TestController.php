<?php 

namespace App\Controllers;

use App\Models\Test;
use App\Models\Offers\OffersModel;

class TestController {
	
	function main(){
//	    $test = new Test();
//	    $test->init();
echo '123';
		return 'test-main route';
	}
	
	
	public function getHelloTest()
  {
    return 'hello-testing route';
  }
	
	
}