<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    MyMiddleware.php
	 */
	
	namespace Http\middleware\auto;
	
	class PreviousPathMiddleware
	{
		public function up()
		{
			// NO return required
		}
		
		public function down()
		{
			if(response('route')->path != '/login')     {
				session_set('previous_path', get_path());   // automatically store current url-path
			}
			
			// NO return required
		}
	}
