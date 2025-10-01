<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    lib\db\mysqliDB.php
	 */

	namespace lib\db;

	class mysqliBind extends \stdClass
	{
		private static $instance = null;
		private $env;
		private $conn;
		public $num_rows    = null;
		public $affected_rows = null;
		public $fieldnames = [];
		public $fieldnamesArray = [];
		public $field_count = null;
		public  $valueArray = [];       // values with named keys
		public  $valueArrayN = [];      // values withOUT named keys for binding
		public  $values     = null;
		public  $types      = null;
		public $inserted_id = null;
		public $queryString = null;


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

		public function PrepereParams($dataArray)
		{
			if(empty($dataArray))   {   // no data given to bind
				return true;
			}
			$valueString        = '';
			$questionMarkString = '';
			$this->types        = '';   // clean up value on earlier requests

			foreach((array) $dataArray as $key => $value)
			{
				$valueString        .= $value.',';
				$questionMarkString .= '?,';
				$this->valueArrayN[] = $value;

				if(is_int( (int)$value) && $key == 'id' ) {    // Integer
					$this->types .= 'i';
				}
				elseif(is_int( $value)) {    // Integer
					$this->types .= 'i';
				}
				elseif(is_float($value)) {   // Double
					$this->types .= 'd';
				}
				elseif(is_string($value) ) {  // String
					$this->types .= 's';
				}
				else {    // Blob and Unknown
					$this->types .= 'b';
				}
			}

			$this->values      = rtrim($valueString,' ,' );
			$this->qMarkString = rtrim($questionMarkString,' ,' );

			if(is_array($this->valueArrayN) &&  is_string($this->types))    {
				return true;
			}
			return false;
		}

		public function QueryBindParams($prepairedStmt = '', $alwaysReturnList = false)
		{
			$stmt = $this->conn->prepare($prepairedStmt);

			if(!empty($this->valueArrayN))   {   // bind params only when they are provided
			$params = array_merge([$this->types], $this->valueArrayN);

			call_user_func_array(array($stmt, 'bind_param'), $params);
//				$stmt->bind_param($this->types, $params);
			}

			$this->conn->query("START TRANSACTION");
			$stmt->execute();
			$result = $stmt->get_result();
			$this->conn->query("COMMIT");

			if(!is_bool($result) && is_array($result->fetch_fields()))
			{   // when update or insert, db-fields not provided
				$this->fieldnames  = $result->fetch_fields();
			}

			$this->error_list   = $stmt->error_list;  // errors or empty array
			$this->queryString  = $prepairedStmt;
			$this->num_rows     = $result->num_rows;
			$this->field_count  = $result->field_count;
			$this->affected_rows= $stmt->affected_rows;
			$this->error_list   = $stmt->error_list;

			if($result->num_rows > 0 ) {
				$data = [];
				while ($row = $result->fetch_assoc()) {
					$data[] = $row;
				}

				if($result->num_rows == 1 && $alwaysReturnList != true) {
					$data = $data[0];   // return single data-set of one record without key 0
				}

				$this->field_count  = $result->field_count;
				$this->fieldsInfo   =  $result->fetch_fields();
				foreach($this->fieldsInfo as $field){
					$fieldnames[]   = $field->name;
				}
				$this->fieldnamesArray = $fieldnames;

				return $data;
			}
			elseif(empty($stmt->error) && $stmt->affected_rows > 0 )    {            // Boolean as result
				if(!empty($stmt->insert_id) && is_numeric($stmt->insert_id)) {  // last inserted ID
					$this->inserted_id = $stmt->insert_id;
				}


				$this->erven = $result;
				return true;
			}
			if(empty($stmt->affected_rows) && $stmt->field_count == 0 && empty($stmt->error_list)) {
				$this->affected_rows = "-1";
				return true;
			}

			return false;
		}
	}
