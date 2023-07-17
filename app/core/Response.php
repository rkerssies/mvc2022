<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    Response.php
	 */

	// the Request data-object is callable in: Controller-, Model-, Library- and MiddleWare-classes

	namespace core;

	class Response extends \stdClass
	{
		private static $instance = null;
		public $data        = [];
		public $route       = [];
		public $services    = [];

		public $middleware  =  [];

		public $view        =  null;

		public $layout      = null;
		public $status      = null;
		public $message    = null;
		public $errors      = null;

		public static function class() { // no constructor in Singleton

			if (!self::$instance) {
				self::$instance = new self();    // or    __CLASS__
			}
			return self::$instance;
		}

		public function __set($name, $value)
		{
			$this->$name = $value;
		}

		public function __get($name)
		{
			return $this->$name;
		}

		public function __isset($name)
		{
			return isset($this->$name);
		}

		public function __unset($name)
		{
			unset($this->$name);
		}

		public function toJson()
		{
			return json_encode(get_object_vars($this));
		}
	}
