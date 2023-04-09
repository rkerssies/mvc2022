<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    MiddleWare.php
	 */
	namespace core;
	
	use lib\files\getFiles;
	
	class MiddleWare
	{
		/*
		 *  Middlewares forms a CHAIN between the request and the controller to its execution and response.
		 *  The request/response objects are free to modify. It's possible to log something, modify the response, redirect, etc.
		 */
		
		private $middlewareObjects = [];
		public $failed = null;
		
		public function __construct($middlewareArray = null)
		{
			$this->mwRoutes = $middlewareArray; // midddleWare called on route
			
			// read and initiate all the MiddleWare-classes in ./app/middleware
			$this->arrayMWauto = (new getFiles())->files('../app/Http/Middleware/auto', 'php');
			$this->arrayMWcall = (new getFiles())->files('../app/Http/Middleware/call', 'php');
		}
		
		public function run($proces = 'up')
		{

			//call all Middleware from auto-folder
			foreach($this->arrayMWauto as $mwFile)
			{
				$nsMW = 'Http\Middleware\auto\\'.basename($mwFile, '.php');    				// create namespace-MiddleWare
				$middlewareObject = new $nsMW();
				if(!(new \ReflectionMethod($nsMW, $proces)))
				{
					Response::class()->status = 400;
					$this->message = 'middleware: '.$nsMW.' with method '.$proces.' failed to run';
					return false;
				}

				(new $nsMW())->$proces();
			}
			
			// run all middleware passed in an array on the current route,
			// middleware named in requested route in routes/web.php, eq: [ 'fruit','index',['login', 'bla'] ],
			if(!empty($this->mwRoutes))
			{
				foreach($this->mwRoutes as $key => $mwFile)
				{
					$dataParams = [];
					if(is_array($mwFile))
					{   // if route-middleware contains values, eq: ['controller','action', ['mw-name'=> ['value1', 'value2'] ]],
						$dataParams = $mwFile;
						$mwFile = $key;
					}
					
					$nsMW='Http\Middleware\call\\'.ucfirst($mwFile).'Middleware';
					$mwRoute[] = $nsMW;
					if(!class_exists($nsMW)) {  // check if middleware-class exists
						$this->message = 'middleware class '.$nsMW.' not found';
						Response::class()->status = 400;
						Response::class()->message = $this->message;
						return false;
					}
					if(!method_exists($nsMW, $proces) && in_arry($proces, ['up', 'down'])){     // check if up or down method exist
						$this->message = 'middleware-method:'.$proces.' on '.$nsMW.' not found' ;
						Response::class()->status = 400;
						Response::class()->message = $this->message;
						return false;
					}
					if(!(new \ReflectionMethod($nsMW, $proces)))
					{
						$this->message = 'middleware-class:'.$nsMW.' with method '.$proces.' failed to run';
						Response::class()->status = 400;
						Response::class()->message = $this->message;
						return false;
					}
					$objMw = new $nsMW();
					call_user_func_array([$objMw,$proces], $dataParams);    // call midleWare-class with (optional) params
					
				}
			}
			if($proces == 'up'){
				Response::class()->middleware = (object) ['middlewareAuto'=> $this->arrayMWauto];
				//Response::class()->middleware->middlewareCallable =   $this->arrayMWcall;
				Response::class()->middleware->middlewareRouteCalled =   $mwRoute;
				Response::class()->middleware->$proces = true;
			}
			else{
				Response::class()->middleware->$proces = true;
			}
			return true;
		}
	}
