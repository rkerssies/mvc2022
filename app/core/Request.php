<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    Request.php
	 */
	
	namespace core;
	
	use lib\encrypt\Salt;
	
	class Request
	{
		private static $instance = null;
		public $method;
		public $get;
//		public $post;
//		public $put;
//		public $patch;
//		public $delete;
		public $message;
		public $hiddenfields      = null;
		
		
		public static function getInstance() { // no constructor in Singleton
			if (!self::$instance) {
				self::$instance = (new self());    // or    __CLASS__
			}
			return self::$instance;
		}
		
		public function all()
		{
			$this->obj = self::getInstance();
			unset($this->obj->obj); // remove recursion of result
			return $this->obj;
		}
		public function make()
		{
			$this->obj  = self::getInstance();
			$out        = (object) [];
			$method     = strtoupper($_SERVER["REQUEST_METHOD"]);
			$message    = '';
			if(! in_array($method, ['GET', 'POST', 'PUT', 'PATCH','DELETE']))   {
				die('<h1>405</h1> request-method '.$out->method.' not allowed');
			}

			if(!empty($_GET)){
				$get = (object) [];
				foreach($_GET as $key => $value)
				{
					$get->$key = strip_tags(htmlspecialchars($value));
				}
				$message = 'GET request-data cleaned';
			}
			
		/// POST /////////////////////////////////
			if(!empty($_POST) && empty($_POST['_method']))
			{
				$out = (object) [];
				foreach($_POST as $key=>$value)
				{
					$out->$key = strip_tags(htmlspecialchars($value));    // clean input
				}
				if(isset($_GET['id']))   {   // add csrf if provided
					$out->id = strip_tags(htmlspecialchars($_GET['id']));
				}
				if(isset($_POST['csrf']) && request()->get->p0 != 'api')   {   // add csrf if provided
					$out->csrf = strip_tags(htmlspecialchars($_POST['csrf']));
				}
				
				if(!empty((array) $out) && $method == 'POST'){
					$message .= ' & POST request-data cleaned';
				}
			}
			
		/// PUT - PATCH - DELETE /////////////////////////////////
			if(strtoupper($_POST['_method'])     == 'PUT'
				|| strtoupper($_POST['_method']) == 'PATCH'
				|| strtoupper($_POST['_method']) == 'DELETE'
				|| $_SERVER['REQUEST_METHOD']    =='PUT'
				|| $_SERVER['REQUEST_METHOD']    =='PATCH'
				|| $_SERVER['REQUEST_METHOD']    =='DELETE' )
			{
				if($_SERVER['REQUEST_METHOD'] != strtoupper($_POST['_method']))  {
					$method = $_POST['_method'];
					unset($_POST['_method']);
					if($method == 'PUT') {  /* create PUT-global ( $_PUT ) */
						$_SERVER["REQUEST_METHOD"]='PUT';
						$method = 'PUT';
					}
					if($method == 'PATCH') {  /* create PUT-global ( $_PUT ) */
						$_SERVER["REQUEST_METHOD"]='PATCH';
						$method = 'PATCH';
					}
					if($method == 'DELETE') {  /* create PUT-global ( $_PUT ) */
						$_SERVER["REQUEST_METHOD"]='DELETE';
						$method = 'DELETE';
						foreach($_GET as $param){
							if(!empty($param) && is_numeric($param)) {
								$_POST['id'] = $param;
							}
						}
					}
						$data = $_POST;     // keep postvars for globals and storage in object
						$_POST = [];        // remove data from global post
				}

				$out = (object) [];
				if(isset($_GET['id']))   {   // add csrf if provided
					$out->id = strip_tags(htmlspecialchars($_GET['id']));
				}
				foreach($data as $key=>$value)
				{
					$out->$key = strip_tags(htmlspecialchars($value));    // clean input
				}
				if(isset($data['csrf']) && request()->get->p0 != 'api')   {   // add csrf if provided
					$out->csrf = strip_tags(htmlspecialchars($data['csrf']));
				}
				
				if(!empty((array) $out) && $method != 'POST'){
					$message .= ' & '.$method.' request-data cleaned';
				}
			}
			//			$_GET = null;       			// force using Request-object and remove globals, TODO populate via Response-class
			//			$_POST = null;              	// force using Request-object and remove globals, TODO populate via Response-class
			$this->obj->method          = $method;
			$this->obj->message         = $message;
			$this->obj->get             = $get;

			$method = strtolower($method);
			if($method != 'get') {          // output is: post OR put OR patch OR delete
				$global = '_'.strtoupper($method);
				$GLOBALS[$global]       = $out;     // store submitted post-, put-, patch- or delete-data in global
				$this->obj->$method     = $out;     // make data available in Request data-object
			}
			return true;
		}
		
		public function getFillable(array $fillableFieldnames, $stringify=false)
		{
			//$this->obj = self::getInstance();
			$data = request();
			$out = (object) [];
			$method = strtolower(request()->method);
			if(! empty( (array) $data) && ! empty( (array)$data->$method)) {
				foreach($data->$method as $key => $value)
				{
					if(in_array($key, $fillableFieldnames)){
						$out->$key = $value;    // creates object with all fillable fields
					}
				}
			}

			if($stringify == true)
			{           // returns all fillable fields in a string, eq:   value1,value2,value3
				$string = '';
				foreach((array) $out as $value)
				{
					$string .= '"'.$value.'",';
				}
				$out = rtrim($string, ",");
			}
			return $out;
		}
		
		//// CSRF
		/*      set CSRF-token in session and return form input-tag
		*		Check in lib\db\Model on Insert- and Update-method (and Delete) if csrf-token is valid
		*       else 401 or return false
		*/
		public function csrf()  //
		{
			$saltObject = new Salt('customCSRF_4_privateKey'.CONFIG['app_key']);
			$secret = $saltObject->generateSecret('60');
			$csrf[$secret] = date('Y-m-d H:i:s');
			
			$_SESSION['csrf'] = $saltObject->encryptSalt(json_encode( $csrf)); // store CSRF-token in session
			
			return '<input type="hidden" name="csrf" value="'.$secret.'" >'; // return input-tag to add in form, via helper-function csrf()
		}
		
		public function checkCsrf($postData = [])
		{
			$method = strtolower(request()->method);
			$saltObject = new Salt('customCSRF_4_privateKey'.CONFIG['app_key']);
			$csrfSession = (array) json_decode($saltObject->decryptSalt($_SESSION['csrf']));
			
			if(empty($postData)) {
				$postData = request();
			}

			if(! array_key_exists(request()->$method->csrf, $csrfSession))  {
				error('403');   // Unauthorized! None or an invalid csrf-token found in session
			}
			
			$to_time = strtotime(date('Y-m-d H:i:s'));
			$from_time = strtotime($csrfSession[$postData->$method->csrf]);

			if(round(abs($to_time - $from_time) / 60,0) > 60000)   {   // form is older than 60 minutes
				back();    // if create own response on rexpired csrf-token
			}
			return true;
		}
		
		public function setKey($key, $value)
		{
			$this->obj = self::getInstance();
			$this->obj->$key = $value;
			return true;
		}
	}
