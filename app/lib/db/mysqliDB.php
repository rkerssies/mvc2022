<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    lib\db\mysqliDB.php
	 */

	namespace lib\db;

	class mysqliDB extends \stdClass
	{
		private static $instance = null;
		private $env;
		private $conn;
		public $num_rows        =null;
		public $affected_rows   = null;
		public $fieldnames      = null;
		public  $valueArray     = [];
		public  $values         = null;  ///
		public $inserted_id     = null;


		public function __construct($host = null, $user = null, $pass = null, $dbname = null)
		{
			$this->env = env('db');
			if(empty($host) && empty($user) && empty($pass) && empty($dbname) ) {
				$host = $this->env->host; $user = $this->env->username; $pass = $this->env->password; $dbname = $this->env->database;
			}
			$this->conn= new \mysqli($host, $user, $pass, $dbname);
			if($this->conn->connect_error)
			{
				die("Connection failed: ".$this->conn->connect_error);
			}
		}


		public static function getInstance()
		{ // no constructor in Singleton
			if (!self::$instance) {
				self::$instance = new self();    // or    __CLASS__
			}
			return self::$instance;
		}


		public  function connect($host = null, $user = null, $pass = null, $dbname = null)
		{
			self::getInstance();
			if(empty($host) && empty($user) && empty($pass) && empty($dbname) ) {
				$host = $this->env->host; $user = $this->env->username; $pass = $this->env->password; $dbname = $this->env->database;
			}

			$this->conn= new self($host, $user, $pass, $dbname);
			if($this->conn->connect_error)
			{
				die("Connection failed: ".$this->conn->connect_error);
			}
		}

		public function querySQL($sql, $list = false)
		{
			self::getInstance();

			$result=$this->conn->query($sql);

			$this->num_rows     = 0;
			if(!is_bool($result))
			{
				$this->num_rows     = mysqli_num_rows($result);
			}
			if(is_bool($result))    {
				$this->affected_rows = mysqli_affected_rows($this->conn);
				if($this->affected_rows == 0) {
					return false;
				}
				return $result;     // true or false from; select and CRUD
			}
			elseif($this->num_rows>=0)      {
				$collection=[];
				while($row = $result->fetch_assoc())
				{
					$collection[]   = (object)$row;
				}
				if($this->num_rows == 1 && $list == false)  // return also a singel record
				{
					return $collection[0];
				}
				return $collection;
			}
			else    {
				error(422);     // header('Location:views/errors/422.html');
			}
			return false;
		}
	}
