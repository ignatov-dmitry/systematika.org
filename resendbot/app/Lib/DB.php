<?php
	namespace App\Lib;
	
	use \App\Config;
	/*
	 * PHP-PDO-MySQL-Class
	 * https://github.com/lincanbin/PHP-PDO-MySQL-Class
	 *
	 * Copyright 2015 Canbin Lin (lincanbin@hotmail.com)
	 * http://www.94cb.com/
	 *
	 * Licensed under the Apache License, Version 2.0:
	 * http://www.apache.org/licenses/LICENSE-2.0
	 * 
	 * A PHP MySQL PDO class similar to the the Python MySQLdb. 
	 */
	//require(dirname(__FILE__) . "/DB.Log.php");
	class DB
	{
		private $Host;
		private $DBName;
		private $DBUser;
		private $DBPassword;
		private $DBPort;
		private $pdo;
		private $sQuery;
		private $bConnected = false;
		private $log;
		private $parameters;
		public $rowCount   = 0;
		public $columnCount   = 0;
		public $querycount = 0;
		
		
		
		public function __construct($DBType='',  $Host='', $DBName='', $DBUser='', $DBPassword='', $DBPort='')
		{
			
		//	print_r(CONFIG);
			
			if(!defined('CONFIG')) Config::init();
			
			$DBType= empty($DBType) ? 'mysql' : $DBType; 
			$Host= empty($Host) ? CONFIG['mysql_credentials']['host'] : $Host;
			$DBName= empty($DBName) ? CONFIG['mysql_credentials']['database'] : $DBName;
			$DBUser= empty($DBUser) ? CONFIG['mysql_credentials']['user'] : $DBUser;
			$DBPassword= empty($DBPassword) ? CONFIG['mysql_credentials']['password'] : $DBPassword;
			$DBPort = empty($DBPort) ? 3306 : $DBPort;
			
			//$this->log        = new Log();
			$this->DBType     = empty($DBType) ? DB_TYPE : $DBType;
			$this->Host       = empty($Host) ? DB_HOST : $Host;
			$this->DBName     = empty($DBName) ? DB_NAME : $DBName;
			$this->DBUser     = empty($DBUser) ? DB_USER : $DBUser;
			$this->DBPassword = empty($DBPassword) ? '' : $DBPassword;
			$this->DBPort	  = empty($DBPort) ? DB_PORT : $DBPort;
			$this->Connect();
			$this->parameters = array();
			
		}
		
		
		private function Connect()
		{
			try {
				$this->pdo = new \PDO($this->DBType . ':dbname=' . $this->DBName . ';host=' . $this->Host . ';port=' . $this->DBPort . ';charset=utf8', 
					$this->DBUser, 
					$this->DBPassword,
					array(
						//For PHP 5.3.6 or lower
						\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
						\PDO::ATTR_EMULATE_PREPARES => false,
						//长连接
						//PDO::ATTR_PERSISTENT => true,
						
						\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
						\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
					)
				);

				//For PHP 5.3.6 or lower
				//$this->pdo->setAttribute(PDO::MYSQL_ATTR_INIT_COMMAND, 'SET NAMES utf8');
				//$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
				//$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				//$this->pdo->setAttribute(PDO::ATTR_PERSISTENT, true);//长连接
				//$this->pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);

				$this->bConnected = true;
				
			}
			catch (PDOException $e) {
				echo $this->ExceptionLog($e->getMessage());
				die();
			}
		}
		
		
		public function CloseConnection()
		{
			$this->pdo = null;
		}
		
		
		private function Init($query, $parameters = "")
		{
			if (!$this->bConnected) {
				$this->Connect();
			}
			try {
				$this->parameters = $parameters;
				$this->sQuery     = $this->pdo->prepare($this->BuildParams($query, $this->parameters));
				
				if (!empty($this->parameters)) {
					if (array_key_exists(0, $parameters)) {
						$parametersType = true;
						array_unshift($this->parameters, "");
						unset($this->parameters[0]);
					} else {
						$parametersType = false;
					}
					foreach ($this->parameters as $column => $value) {
						//echo $parametersType;
						$this->sQuery->bindParam($parametersType ? intval($column) : ":" . $column , $this->parameters[$column]); //It would be query after loop end(before 'sQuery->execute()').It is wrong to use $value.
						
					}
				}
				
				$this->succes = $this->sQuery->execute();
				$this->querycount++;
			}
			catch (PDOException $e) {
				echo $this->ExceptionLog($e->getMessage(), $this->BuildParams($query));
				die();
			}
			
			$this->parameters = array();
		}
		
		private function BuildParams($query, $params = array()){
			if (!empty($params)) {
				$array_parameter_found = false;
				foreach ($params as $parameter_key => $parameter) {
					if (is_array($parameter)){
						$array_parameter_found = true;
						$in = "";
						foreach ($parameter as $key => $value){
							$name_placeholder = $parameter_key."_".$key;
							// concatenates params as named placeholders
								$in .= ":".$name_placeholder.", ";
							// adds each single parameter to $params
							$params[$name_placeholder] = $this->pdo->quote($value);
						}
						$in = rtrim($in, ", ");
						$query = preg_replace("/:".$parameter_key."/", $in, $query);
						// removes array form $params
						unset($params[$parameter_key]);
					}
				}
				// updates $this->params if $params and $query have changed
				if ($array_parameter_found) $this->parameters = $params;
			}
			return $query;
		}
		
		
		public function query($query, $params = null, $fetchmode = \PDO::FETCH_ASSOC)
		{
			$query        = trim($query);
			$rawStatement = explode(" ", $query);
			$this->Init($query, $params);
			$statement = strtolower($rawStatement[0]);
			if ($statement === 'select' || $statement === 'show') {
				return $this->sQuery;/*->fetchAll($fetchmode);*/
			} elseif ($statement === 'insert' || $statement === 'update' || $statement === 'delete') {
				return $this->sQuery;/*->rowCount();*/
			} else {
				return NULL;
			}
		}

		/*
			* создает пустую запись в бд 
			* Возвращает ID в случае успеха
		*/
		public function create($nametable, $id=''){
			$nametable = addslashes($nametable);
			$result = $this->query("INSERT INTO `{$nametable}` (`id`) values ('{$id}')"); 
			if($result) return $this->lastInsertId(); else return false; // в случае успеха, возвращает ид записи
			
		}
		
		/*
			* Функция редактирования записи в бд
			* Берет названия и значения из массива $data
			* $noarray - какие ключи не брать во внимание
		*/
		public function edit($nametable, $data, $noarray=array()){
			
			$nametable = addslashes($nametable);
			$id=addslashes($data['id']);	
			
			if(!is_array($noarray)) $noarray = array($noarray);
			foreach($data as $key => $value){
				$stop=0;
				if(in_array($key, $noarray)) $stop=1; 
				if($stop!=1){
					
					$this->query("UPDATE `{$nametable}` SET `".addslashes($key)."`='".addslashes($value)."' WHERE `id`='".$id."'");
					
				}
			}
			return true;
		}
		
		
		public function lastInsertId()
		{
			return $this->pdo->lastInsertId();
		}

		public function prepare($param)
		{
			return $this->pdo->prepare($param);
		}
		
		
		public function column($query, $params = null)
		{
			$this->Init($query, $params);
			$resultColumn = $this->sQuery->fetchAll(PDO::FETCH_COLUMN);
			$this->rowCount = $this->sQuery->rowCount();
			$this->columnCount = $this->sQuery->columnCount();
			$this->sQuery->closeCursor();
			return $resultColumn;
		}
		public function row($query, $params = null, $fetchmode = PDO::FETCH_ASSOC)
		{
			$this->Init($query, $params);
			$resultRow = $this->sQuery->fetch($fetchmode);
			$this->rowCount = $this->sQuery->rowCount();
			$this->columnCount = $this->sQuery->columnCount();
			$this->sQuery->closeCursor();
			return $resultRow;
		}
		
		
		public function single($query, $params = null)
		{
			$this->Init($query, $params);
			return $this->sQuery->fetchColumn();
		}
		
		
		private function ExceptionLog($message, $sql = "")
		{
			$exception = 'Unhandled Exception. <br />';
			$exception .= $message;
			$exception .= "<br /> You can find the error back in the log.";
			
			if (!empty($sql)) {
				$message .= "\r\nRaw SQL : " . $sql;
			}
			//$this->log->write($message, $this->DBName . md5($this->DBPassword));
			//Prevent search engines to crawl
			header("HTTP/1.1 500 Internal Server Error");
			header("Status: 500 Internal Server Error");
			return $exception;
		}
	}