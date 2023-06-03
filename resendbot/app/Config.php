<?php 

namespace App;


class Config {
	
	function __construct(){
		self::init();
	}
	
	public static function init(){
		return require_once __DIR__ . "/../config.php";
	}
	
}