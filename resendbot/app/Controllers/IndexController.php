<?php 

namespace App\Controllers;
use \App\Lib\Controller;

use \App\Views\IndexView;
use \App\Models\Bitrix\CRest;

class IndexController extends Controller {
	
	public $View = '';
	
	
	function __construct(){
		$this->View =  new \App\Views\IndexView();
	}
	
	function main(){
		$User = new \App\Models\User\User;
		$User->sessionInit();
		
		
		$data = ['auth'=>$User->_id, 'session_id'=>$User->sessionId, 'userdata'=>$User->getUser($User->_id)]; 
		
		//print_r($data);
		$this->View->IndexView($data);
	}
	
	
	public function getHelloWorld()
  {
    return 'hello-world route';
  }
	
	public function getLogout(){
		$User = new \App\Models\User\User;
		$User->sessionInit();
		$User->logOutUser($User->_id);
	}

	public function getTest(){

    }

    public function getInstall(){
        $result = CRest::installApp();
        if($result['rest_only'] === false):
            echo '<head>
                <script src="//api.bitrix24.com/api/v1/"></script>';
                if($result['install'] == true):
                    echo '<script>
                        BX24.init(function(){
                            BX24.installFinish();
                        });
                    </script>';
                endif;
            echo '</head>
            <body>';
            if($result['install'] == true):
                echo 'installation has been finished';
            else:
                echo'installation error';
            endif;
            echo'</body>';
        endif;
    }

    public function getBitrix(){
        $result = CRest::call('profile');

        echo '<pre>';
        print_r($result);
        echo '</pre>';

    }
}