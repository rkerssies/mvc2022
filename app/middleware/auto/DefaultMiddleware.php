<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    MyMiddleware.php
	 */
	
	namespace middleware\auto;
	
	use core\Response;
	
	class DefaultMiddleware
	{
		// NB: more separate MiddleWare-classes can be made in the ./middleware - folder
		public function up()
		{
			// set data in Request data-object via helper-functions
//			response()->bla = 'blabla';
//			response()->hoi = ['haai','doei'];
//			// set data in Request data-object via instance
//			$response = (new Response())->class();
//			$response->data = 'set with Request-object, not helper-function';
//
//			echo 'MyMiddleWare UP<br>';
			
			
			// NO return required
		}
		
		public function down()
		{
			// add here functionality to execute AFTER a controller is called
			//echo 'MyMiddleWare DOWN; value set Response-class in MyMiddleWare-up: '.response()->bla.'<br>';   //
			//echo (response()->toJson());
			
			// NO return required
		}
	}
