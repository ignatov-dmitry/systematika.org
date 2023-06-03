<?php 

namespace App\Models\Route;

class Route {
	
	
	function __construct(){
		init();
	}
	
	public static function init(){
		
		//echo \Edtgbot\Controllers\IndexController::main();
		//var_dump (  );
		
		$router = new \Buki\Router([
		  'paths' => [
			  'controllers' => 'app/Controllers',
		  ],
		  'namespaces' => [
			  'controllers' => "\App\Controllers",
		  ],
		]);
		
		//echo __DIR__ .'/../../';
		
				/*
		$router->get('/', function() {
			return 'Hello World!';
		});
		*/
		
		//print_r($router->getRoutes());
		
		$router->controller('/', 'IndexController');
		// Регистрируем контроллеры в роутере
		$controllers = static::findControllers('app/Controllers');
		for($i=0;$i<count($controllers);$i++){
			$router->controller('/'.$controllers[$i], ucfirst($controllers[$i]).'Controller');
		}
		
		
		
		//$router->get('/controller', 'TestController@main');

		$router->run();
		
		
		
	}
	
	
	
	function loadControllers(){
		
	}
	
	 public static function findControllers($path){
		  // Проверка на существование каталога 
        $dh  = opendir($path);
		while (false !== ($filename = readdir($dh))) {
			if($filename !== '.' and $filename !== '..')
				$files[] = mb_strtolower(str_replace('Controller.php', '', $filename));
		}
		return $files;

	}
	
	
}