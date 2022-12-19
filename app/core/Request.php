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
		public $post;
		public $message;
		
		
		public static function getInstance() { // no constructor in Singleton
			if (!self::$instance) {
				self::$instance = new self();    // or    __CLASS__
			}

			return self::$instance;
		}
		
		public function all()
		{
			$this->obj = self::getInstance();
			$out = (object) [];
			
			$method = strtoupper($_SERVER["REQUEST_METHOD"]);
			if(! in_array($method, ['GET', 'POST', 'PUT', 'PATCH','DELETE']))   {
				die('<h1>405</h1> request-method '.$method.' not allowed');
			}
			$out->method = $method;

			if(!empty($_GET)){
				$out->get = (object) [];
				foreach($_GET as $key => $value)
				{
					$out->get->$key = strip_tags(htmlspecialchars($value));
				}
				$message = 'GET request-data cleaned';
			}
			
			if(!empty($_POST))
			{
				$out->post = (object) [];
				foreach($_POST as $key=>$value)
				{
					$out->post->$key = strip_tags(htmlspecialchars($value));    // clean input
				}
				if(isset($_GET['id']))   {   // add csrf if provided
					$out->post->id = strip_tags(htmlspecialchars($_GET['id']));
				}
				if(isset($_POST['csrf']))   {   // add csrf if provided
					$out->post->csrf = strip_tags(htmlspecialchars($_POST['csrf']));
				}
				if(isset($_POST['_method']))   {   // add method (PUT, PATCH, DELETE, etc) if provided
					$out->post->_method = strip_tags(htmlspecialchars($_POST['_method']));
				}
				
				if(! empty( (array) $out->get) && !empty((array) $out->post)){
					$message .= ' & ';
				}
				if(!empty((array) $obj->post)){
					$message .= 'POST request-data cleaned';
				}
			}
			$out->message = $message;
			
//			$_GET = null;       			// force using Request-object and remove globals, TODO populate via Response-class
//			$_POST = null;              	// force using Request-object and remove globals, TODO populate via Response-class
			$this->method = $out->method;
			$this->get = $out->get;
			$this->post = $out->post;
			$this->message = $out->message;
			
			unset($this->obj);
			return $this;
		}
		
		public function getFillable(array $fillableFieldnames, $stringify=false)
		{
			$this->obj = self::getInstance();
			$data = $this->obj->all();
			
			$out = (object) [];
			if(! empty( (array) $data) && ! empty( (array)$data->post)) {
				foreach($data->post as $key => $value)
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
			$saltObject = new Salt('customCSRF_4_privateKey'.CONFIG['app_key']);
			$csrfSession = (array) json_decode($saltObject->decryptSalt($_SESSION['csrf']));
			

			if(empty($postData)) {
				$postData = request()->all();
			}

			if(!array_key_exists(request()->post->csrf, $csrfSession))
			{
				error('400');   // unauthorized ! no or invalid csrf-token found in session
			}
			
			$to_time = strtotime(date('Y-m-d H:i:s'));
			$from_time = strtotime($csrfSession[$postData->post->csrf]);
			
			if(round(abs($to_time - $from_time) / 60,0) > 60000)   {   // form is older than 60 minutes
				back();    // if create own response on rexpired csrf-token
			}

			return true;
		}
	}
