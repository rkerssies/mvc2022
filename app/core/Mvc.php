<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    Mvc.php
	 */
	
	namespace core;
	
	use core\Session;
	use core\Response;
	use lib\files\getFiles;
	use lib\encrypt\Salt;
	
class mvc
{
	private $config;                        // contains all init-configuration
	public $controller          = null;     // updates in route-method
	public $action              = null;     // updates in route-method
	public $route               = [];       // updates in route-method
	public $url                 = null;     // updates in route-method
	public $path                 = null;     // updates in route-method
	private $middlewareArray    = [];       // updates in route-method
	private $params;                        // used for getting params into view-files and the layout
	private $requestParams      = [];
	private $viewPath;
	private $services           = null;

	public function __construct()
	{
		/* Activate autoloader on app-folder to initiate classes by there namespace */
		spl_autoload_register(
			function ($class) {
				// autoloader has app-folder as root
				$class = strtolower(str_replace("\\","/",$class));
				include '../app/'.$class.'.php'; //     ../app/lib/files/getFiles.php
			}
		);
		spl_autoload_extensions('.php');

		/*
		 *  Setting global constanses CONFIG and DB + making config available for the MVC-proces
		 */
		$parts = parse_ini_file('../app/config/config.ini');    // get config-data in constant-var
		$this->config = (object) $parts;
		define( 'DB', $parts['db']); // config-db available in whole framework
		unset($parts['db']);
		define( 'SMTP', $parts['smtp']); // config-db available in whole framework
		unset($parts['smtp']);
		define( 'CONFIG', $parts);  // config-constants available in whole framework
		
		/* Bootsrap reads all files in core-folder for eq: Request-class, Middleware-class and
		 *  a singleton Response-class as dataobject and other classes
		 * Also several helper-functions are read */
		if($this->bootstrap('../app/core') == false)  {
			die('error: failed bootstrap');
		}
		
		/*
		 *  Encrypted fingerprint (remote ipaddress + browser + due-time)
		 *  additional security on session-id
		 */
		if(! (new Session())->run())    {
			die('FATAL !  Session hijack');
		}
	}

	/* Main front-entrance of the application */
	public function site()
	{
		if(session_isset('messagebar'))
		{    // places message for the messageBar in top of the screen in session-key
			 // into response (only available for this single request), see app/views/components/messagebar.phtml
			response_set('messagebar', session_get('messagebar'));
			sessionkey_unset('messagebar');
		}
		
		
		if($this->route() == false) {
			error('404');       //die('<h1>404</h1> invalid url');
		}
		
		// call down-methods on used MiddleWare (auto and called)
		$middlewareObj = new MiddleWare($this->middlewareArray);
		if(!$middlewareObj->run())    {                        // all MiddleWare-classes in ./middleware/auto automatically UP-method
			die($middlewareObj->failed->message);
		}
		
		if(! $this->controller()) {
			die($this->message);
		}
		
		// call down-methods on used MiddleWare (auto and called)
		if(!$middlewareObj->run('down'))   {        // called MiddleWare-classes DOWN-method called by requested route
			die($middlewareObj->failed->message);
		}
//		view removed


		
		// call configured layout according to config.ini
		 if(! $this->layout())  {
			 die($this->message);
		 }

	}
	
	///////////////////////////////////////////////////////
	
	/*
	 *      Method used for reading all class-files and helper-functions in app/core
	 */
	private function bootstrap($path)
	{   // read all php-files (non-namespaced) in core-folder
		foreach(glob($path.'/*.php') as $file)  {
			include_once($file);
		}
		return true;
	}
	
	/*
	 *      Method used for calling the route
	 */
	private function route()
	{
		$routes  = include('../app/routes/web.php');
		$routesValues = array_keys($routes);

		$path = str_replace( (string) $this->config->base_path, '/', (string)  $_SERVER['REQUEST_URI']);
		$this->url =$this->config->domain. $_SERVER['REQUEST_URI'];
		$this->path = $path;
		Response::class()->route = (object) ['domain'=> $this->config->domain];
		Response::class()->route->url =   $this->url;
		Response::class()->route->path =  $this->path;
		
		$path = explode('?', $path)[0]; // remove QSA from path, if is set. Eq: /fruits/var_value1/var_value2?page=3
		$givenPathToCheck = ltrim($path, '/');

		foreach($routesValues as $route)
		{
			$routePart      = ltrim(explode('@', $route)[1], '/');
			$methodsToCheck = ltrim(explode('@', $route)[0], '/');

			
			// alter patterns based on route-part to check on path in request-url;
			$routePatternToCheck = preg_replace(',/\$[a-zA-Z0-9_-]+\?,', '[/]?[a-zA-Z0-9_-]*', $routePart);
			$routePatternToCheck = preg_replace(',/\$[a-zA-Z0-9_-]+,', '/[a-zA-Z0-9_-]+', $routePatternToCheck);
			$routePatternToCheck = str_replace('/*\?,', '[/]?[a-zA-Z_-0-9]*', $routePatternToCheck); // find astrix before adding atrixes in reg-expressions
			$routePatternToCheck = str_replace('/*', '/[a-zA-Z_-0-9]+', $routePatternToCheck);
			$routePatternToCheck = str_replace('/#?', '[/]?[0-9]*', $routePatternToCheck);
			$routePatternToCheck = str_replace('/#', '/[0-9]+', $routePatternToCheck);
			$routePatternToCheck = str_replace('/%?', '[/]?[a-zA-Z]*', $routePatternToCheck);
			$routePatternToCheck = str_replace('/%', '/[a-zA-Z]+', $routePatternToCheck);

			// get vars with value from path and get var-name from matched-route
			if(preg_match(',^'.$routePatternToCheck.'[\/]?$,' ,  $givenPathToCheck))
			{
				
				$requestParam = array();
				// check if request-method can be found in route-methods (before @-sign)
				if(! preg_match('~'.strtolower(request()->method).'~', strtolower($methodsToCheck) ) )   {
					error('405');   //die('<h1>405</h1> method not supported on route-request');
				}

				if(strpos($route, '$'))     {     // if $ in route, then put var-name and value from url in var.
					$pathParts = explode('/',$givenPathToCheck);
					foreach(explode('/', $routePart) as $key => $route_param)
					{
						if(substr($route_param, 0,1) == '$')    {
							$k = rtrim(ltrim( $route_param, '$'),'?');
							$v = $pathParts[$key];
							$requestParam[$k] = $v;
						}
					}
				}
				$this->requestParams    = $requestParam;
				$this->controller       = $routes[$route][0];
				$this->action           = $routes[$route][1];
				$this->middlewareArray  = $routes[$route][2];
				return true;          // match found, stop looking for other matches
			}
		}
		// set corresponding controller and action for found route
		if( empty($this->controller) || empty($this->action))  {  // no matching route found
			error('404');   //die('<h1>404</h1>');
		}
		return false;
	}
	
	
	/*
	 *      Method 'services' is calling the needed service-class
	 */
	private function services($layoutName)
	{
		$serviceHandler= ( new Services());
		$services = $serviceHandler->handler($this->obj, $layoutName) ;
		if(empty($services))   {
			$this->message = 'running Services failed on:'.$serviceHandler->failMessage;
			return false;
		}
		
		$this->services = (object) $this->services;
		foreach( (array) $services as $sName => $serviceValue)  {
			$this->services->$sName = (object) $serviceValue;  // response Service avaliable in Mvc-object ($this->var) and all views/layout ($var)
		}
		
		return true;
	}
	
	/*
	 *      Method 'controller' calling in the MVC-process
	 */
	private function controller()
	
	{
		if(! is_file('../app/Http/Controllers/'.ucfirst($this->controller).'Controller.php'))    {
			$this->message =  'controller-file not found; '.'../app/Http/Controllers/'.ucfirst($this->controller).'Controller.php';
			Response::class()->status = 404;
			Response::class()->message = $this->message;
			return false;
		}
		
		// initiate controller-class
		include_once('../app/Http/Controllers/'.ucfirst($this->controller).'Controller.php');
		$controller = 'Http\Controllers\\'.ucfirst($this->controller).'Controller';
		if(class_exists($controller))   {
			$this->obj       = new $controller();		// make an instance (object) of the controller-class
		}
		else    {
			$this->message = 'class-name <b>'.$controller.'</b> not correct defined in file: '.'../app/Http/controllers/'.ucfirst($this->controller).'Controller.php';
			Response::class()->status = 404;
			Response::class()->message = $this->message;
			return false;
		}
		// call action on controller-class
		if(is_object($this->obj) && method_exists($this->obj, $this->action))
		{
			// Support type-hinted parameter called in controller-action
			$ReflectionMethod   = new \ReflectionMethod($controller, $this->action);
			$parameters = $ReflectionMethod->getParameters();
			
			if(count($parameters) > 5){
				$this->message = 'action <b>'.$this->action.'</b> can\'t contain more than 5 params in file: '.'../app/Http/Controllers/'.ucfirst($this->controller).'Controller.php';
				Response::class()->status = 404;
				Response::class()->message = $this->message;
				return false;
			}
			
			$i          = 0;
			foreach($parameters as $key => $parameter)      {
				$p_name = ('param'.$i);
				if(! $parameter->getType()) {   // fill var with data
					$$p_name = $this->requestParams[$parameter->name];
					$propertyName = $parameter->name;
					$this->obj->$propertyName = $this->requestParams[$propertyName]; // making named route-parameters available in view
				}
				else { // make instance on typehinted action-parameter
					$ns = $parameter->getType()->getName();
					if (! class_exists($ns)) {  // make instace of known namespace
						$this->message =  'The class '.$ns.'() doen\'t exists';
						Response::class()->status = 404;
						Response::class()->message = $this->message;
						return false;
					}
					$$p_name = new $ns();
				}
				$i++;
			}
			
			// function to call controller-action with (optional type-hinted params)
			call_user_func_array(array($this->obj,$this->action), [$param0,$param1,$param2,$param3,$param4,$param5,$param6]);
			// alternative way to call action on controller with type-hinted params: $this->obj->$method($param0,$param1,$param2,$param3,$param4 );// call the action on the controller-object

			Response::class()->route->controllerClass   =   ucfirst($this->controller).'Controller';
			Response::class()->route->controllerRoute   =  $this->controller;
			Response::class()->route->action            =  $this->action;
			Response::class()->route->routeMiddleware   =  $this->middlewareArray;
		}
		else    {
			$this->message = 'action <b>'.$this->action.'</b> doesn\'t exist in class '.$controller. ' in file: '.'../app/Http/ontrollers/'.ucfirst($this->controller).'Controller.php';
			Response::class()->status   = 404;
			Response::class()->message  = $this->message;
			return false;
		}
		return true;
	}
	
	/*
	 *      Method 'layout' calling in the MVC-process
	 */
	private function layout()
	{
	
	// get required layout-set, bij default in config.ini ore schedueled
		$layoutName = $this->config->layoutName;
		if($this->config->ScheduledLayout == true){
			$schedule  = include('../app/config/layoutSchedule.php');
			foreach($schedule as $changeLayout)
			{
				$today = new \DateTime(); // Today
				$beginSchedule = new \DateTime($changeLayout['from']);
				$endSchedule  = new \DateTime($changeLayout['till']);
				
				if ($today->getTimestamp() >= $beginSchedule->getTimestamp() &&
					$today->getTimestamp() < $endSchedule->getTimestamp())
				{
					$layoutName = $changeLayout['layoutName'];
				}
			}
		}
		$this->layoutName = $layoutName;
		
		// call Services
		if(! $this->services($layoutName)) {
			die($this->message);
		}
		
		if(!empty($this->services->pagination->scalar))
		{
			$this->pagination = $this->services->pagination->scalar;
		}
		
		// get the required view-file and the params to pass to the view from the controller-action
		$this->useView($this->obj->useView);
		$this->params = $this->obj; // object-params available in view-method
		
		// convert object-properties to variables, eq: '$this->view' becomes '$view' in the view/layout-file
		foreach((array) $this->services as $key => $service){
			if($this->services->$key->scalar){
				$$key = $this->services->$key->scalar;
			}
			else {
				$$key = $this->services->$key;
			}
		}
		
		if(file_exists('../app/layouts/'.$layoutName.'/layout.phtml'))
		{   // load layout
			Response::class()->status = 200;
			Response::class()->layout = 'app/layouts/'.$layoutName.'/layout.phtml';
			Response::class()->message = 'completed proces for rendered an application with the MVC';
			include('../app/layouts/'.$layoutName.'/layout.phtml');
		}
		else {
			Response::class()->status = 400;
			$this->message = 'layout-file app\/layouts\/'.$layoutName.'\/layout.phtml not found';
			return false;
		}
	}

	/*
	 *      Method 'useView' called within view-files in controller-actions (optional; for API-requests)
	 */
	public function useView($pathView = null)   // method to call a view in a controller-action
	{

		if(!empty ($pathView))
		{
			$pathView=ltrim($pathView, '.');
			$this->viewPath = str_replace('.', '/', ltrim($pathView, '/'));
		}
		Response::class()->view =  (object) ['view' => $pathView, 'view-path' => 'app/views/'.$this->viewPath.'.phtml'];
	}
	
	/*
	 *      Method 'view' called within the layout-view to pullin views
	 */
	public function view()
	{
		$pagination = $this->pagination;
		foreach( (array) $this->params as $key => $value) {   // simplifying var-names for use the in view-file
			$$key = $value;
		}
		$pagination = $this->pagination;

		if( file_exists('../app/views/'.$this->viewPath.'.phtml'))  {
			include('../app/views/'.$this->viewPath.'.phtml');	// load view
		}
		else    {
			$this->message = 'view-file doesn\'t exists; ../app/views/'.$this->viewPath.'.phtml';
			Response::class()->status = 400;
			Response::class()->message = $this->message;
		}
	}
}


