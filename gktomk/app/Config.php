<?php 

namespace GKTOMK;


class Config {
	
	function __construct(){
		init();
	}
	
	public static function init(){
		return require_once __DIR__ . "/../config.php";
	}
	
}