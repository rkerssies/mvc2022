<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    02/07/2022
	 * File:    Services.php
	 */
	
	namespace core;
	
	use core\Mvc;
	
	class Services extends Mvc
	{
		/*
		 *      Making services available in views and layouts.
		 *      eq: all css-links are generated and passed trouh to the layout.
		 */
		public $failMessage = null;
		
		/*
		 *      Method that returns an array with all the accepted/required services
		 */
		private function register()
		{
				return [    // all required services
					'nav',
					'css',
					'js',
					'meta',
					'pagination',
				];
		}
		
		public function handler($params = null, $layoutName= null)
		{
			$services = (object)  [];
			foreach($this->register() as $service)
			{
				$nsService = 'services\\'.$service.'Service';
				$result = (new $nsService($layoutName))->call($params);
				$services->$service =  $result;  // nb: if containing html-tags, use: htmlspecialchars($html_string)

				if(empty($services) ) { // && empty($services->$service)
					$this->failMessage = $nsService. ' response ';
					return false;
				}
			}
			return  $services;
		}
		
	}
