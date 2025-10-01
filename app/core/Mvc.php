<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * Update:  29/09/2025
	 * File:    Mvc.php
	 */

	namespace core;

	use core\Session;
	use core\Response;
	use core\Request;
	// use lib\files\getFiles;
	// use lib\encrypt\Salt;
	use \Exception;	

class mvc extends \stdClass
{
	private $config;                        // contains all init-configuration
	public $controller          = null;     // updates in route-method
	public $action              = null;     // updates in route-method
	public $route               = [];       // updates in route-method
	public $api                 = null;
	public $url                 = null;     // updates in route-method
	public $path                = null;     // updates in route-method
	private $middlewareArray    = [];       // updates in route-method
	private $params;                        // used for getting params into view-files and the layout
	private $requestParams      = [];
	private $viewPath;
	private $services           = null;

	public function __construct()
	{
		require '../vendor/autoload.php';	// read composer-packages in /vendor


		/* Activate autoloader on app-folder to initiate classes by there namespace */
		spl_autoload_register(
			function ($class) {
				// autoloader has app-folder as root
				$class = strtolower(str_replace("\\","/",$class));
				include '../app/'.$class.'.php'; //     ../app/lib/files/getFiles.php
			}
		);
		spl_autoload_extensions('.php');


		/* Bootsrap reads all files in core-folder for eq: Request-class, Middleware-class and
		 *  a singleton Response-class as dataobject and other classes
		 * Also several helper-functions are read */
		if($this->bootstrap('../app/core') == false)  {
			die('error: failed bootstrap');
		}

		/* Load configuration from .env
			set default timezone if APP_TIMEZONE exists in .env
			define constants per prefix in .env (eq: DB_HOST becomes DB::HOST)
			populate global $_ENV array with env values
			define global env() function to get env-values 
		*/
		try {
			loadEnv();
		} 
		catch (Exception $e) {
			die("FATAL !  ".$e->getMessage());
		}

			// load envoronmet-variables from .env-file into $_ENV-array and env() function

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
		(new Request())->make();      // Initate request-object

		if($this->route() == false) {
			error('404');         //die('<h1>404</h1> invalid url');
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
		//	view is placed in the layout-method

		if($this->api == true)      {
			echo json_encode(['No json-data response received from the ApiCointroller-action !']);
			die;
		}

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
			require_once($file);
		}
		return true;
	}

	/*
	 *      Method used for calling the route
	 */
	private function route()
	{

		if(request()->get->p0 == 'api'){    // api-routes
			$this->api = true;
			$routes  = include('../app/routes/api.php');
		}
		else    {                           // web-routes
			$routes=include('../app/routes/web.php');
		}

		$routesValues = array_keys($routes);

		$path = str_replace( (string) env('app')->basepath, '/', (string)  $_SERVER['REQUEST_URI']);
		$this->url =env('app')->domain. $_SERVER['REQUEST_URI'];
		$path = str_replace('/api', '', $path);   // remove prefix:  /api   from path
		$this->path = $path;

		Response::class()->route = (object) ['domain'=> env('app')->domain];
		Response::class()->route->url =   $this->url;
		Response::class()->route->path =  $this->path;

		$path = explode('?', $path)[0]; // remove QSA from path, if is set. Eq: /fruits/var_value1/var_value2?page=3
		$givenPathToCheck = ltrim($path, '/');
		$methodFound = null;

		foreach($routesValues as $route)
		{
			$routePatternToCheck = null;
			$methodsToCheck = ltrim(explode('@', $route)[0], '/');
			$routePart      = ltrim(explode('@', $route)[1], '/');

			// alter patterns based on route-part to check on path in request-url;
			$ArrayMethodsToCheck = explode('-', $methodsToCheck);
			if(in_array(strtolower(request()->method), $ArrayMethodsToCheck))   {
				$methodFound = strtolower(request()->method);
			}
			if(empty($this->path)) { $this->path = '/'; }   // exception when url-path is just a slash ( / )
			$routePatternToCheck = preg_replace(',[$][a-zA-Z0-9_-]+[\?],', '[/]?[a-zA-Z0-9_-]*', $routePart);
			$routePatternToCheck = preg_replace(',[$][a-zA-Z0-9_-]+,', '[/]?[a-zA-Z0-9_-]+', $routePatternToCheck);
			$routePatternToCheck = str_replace('/*\?,', '[/]?[a-zA-Z_-0-9]*', $routePatternToCheck); // find astrix before adding atrixes in reg-expressions
			$routePatternToCheck = str_replace('/*', '[/]?[a-zA-Z_-0-9]+', $routePatternToCheck);
			$routePatternToCheck = str_replace('/#?', '[/]?[0-9]*', $routePatternToCheck);
			$routePatternToCheck = str_replace('/#', '[/]?[0-9]+', $routePatternToCheck);
			$routePatternToCheck = str_replace('/%?', '[/]?[a-zA-Z]*', $routePatternToCheck);
			$routePatternToCheck = str_replace('/%', '[/]?[a-zA-Z]+', $routePatternToCheck);
			//$routePatternToCheck = str_replace('/[a-zA-Z0-9-_]+', '[/]?[a-zA-Z0-9-_]+', $routePatternToCheck);

			// check created patern on url-path with optional QSA
			if(preg_match(',^[/]?'.$routePatternToCheck.'[?]?[a-z0-9&-_=&]*$,', $this->path, $pathFound))   {
				$pathFound = $pathFound[0];
			}

			if ( !empty($pathFound))  {
				if(empty($methodFound))  {   // method not found with preg_match on chars before @-sign
					error('405');   //die('<h1>405</h1> method not supported on route-request');
				}
				$requestParam = array();

				if(strpos($route, '$'))     {     // if $ in route, then create var-name and its value from url in var.
					$pathParts = explode('/',$givenPathToCheck);

					foreach(explode('/', $routePart) as $key => $route_param)
					{
						if(substr($route_param, 0,1) == '$')    {
							$k = rtrim(ltrim( $route_param, '$'),'?');
							$v = $pathParts[$key];
							$requestParam[$k] = $v;
						}
					}
					response_set('requestParams', (object) $requestParam);
				}
				$this->method           = $methodFound;
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
			if(request()->get->p0 != 'api') {
				$this->message =  'controller-file not found; '.'../app/Http/Controllers/'.ucfirst($this->controller).'Controller.php';
			}
			else {
				$this->message =  'API-service on the MVC2022-project by InCubics.net (c)'.date('Y').'-'.(date('Y')+1);
			}
			Response::class()->status = 404;
			Response::class()->message = $this->message;
			return false;
		}

		// initiate controller-class
		include_once('../app/Http/Controllers/'.ucfirst($this->controller).'Controller.php');
		$controller = '\Http\Controllers\\'.ucfirst($this->controller).'Controller';
		if(class_exists($controller))   {
			$this->obj       = new $controller();		// make an instance (object) of the controller-class
		}
		else    {
			$this->message  = 'class-name <b>'.$controller.'</b> not correct defined in file: '
								.'../app/Http/controllers/'.ucfirst($this->controller).'Controller.php';
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
				$this->message = 'action <b>'.$this->action.'</b> can\'t contain more than 5 params in file: '
								.'../app/Http/Controllers/'.ucfirst($this->controller).'Controller.php';
				Response::class()->status = 404;
				Response::class()->message = $this->message;
				return false;
			}
			if(request()->get->p0 != 'api')
			{
				$i=0;
				foreach($parameters as $key=>$parameter)
				{
					$p_name=('param'.$i);
					if(!$parameter->getType())
					{   // fill var with data
						$$p_name=$this->requestParams[$parameter->name];
						$propertyName=$parameter->name;
						$this->obj->$propertyName=$this->requestParams[$propertyName]; // making named route-parameters available in view
					}
					else
					{ // make instance on typehinted action-parameter
						$ns=$parameter->getType()
							->getName();
						if(!class_exists($ns))
						{  // make instace of known namespace
							$this->message='The class '.$ns.'() doen\'t exists';
							Response::class()->status=404;
							Response::class()->message=$this->message;
							return false;
						}
						$$p_name=new $ns();
					}
					$i++;
				}
			}

			// function to call controller-action with (optional type-hinted params)
			call_user_func_array(array($this->obj,$this->action), [$param0,$param1,$param2,$param3,$param4,$param5,$param6]);
			// alternative way to call action on controller with type-hinted params: $this->obj->$method($param0,$param1,$param2,$param3,$param4 );// call the action on the controller-object
			Response::class()->route->controllerClass   =   ucfirst($this->controller).'Controller';
			Response::class()->route->controllerRoute   =  $this->controller;
			Response::class()->route->action            =  $this->action;
			Response::class()->route->routeMiddleware   =  $this->middlewareArray;
		}
		elseif(is_object($this->obj) && empty($this->action) && request()->get->p0 == 'api')    // base
		{

			die('api action GO');
		}
		else    {
			$this->message = 'action <b>'.$this->action.'</b> doesn\'t exist in class '.$controller. ' in file: '
							.'../app/Http/ontrollers/'.ucfirst($this->controller).'Controller.php';
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
		$layoutName = env('app')->layoutname;
		if(env('app')->ScheduledLayout == true){
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
		if(! $this->services($layoutName))  {
			die($this->message);
		}

		if(!empty($this->services->pagination->scalar))     {
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


