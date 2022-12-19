<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    MyMiddleware.php
	 */
	
	namespace middleware\call;
	
	use core\Response;
	
	class LoginMiddleware
	{
		// NB: more separate MiddleWare-classes can be made in the ./middleware - folder
		public function up()
		{
			if( !session_isset('login') && response()->route->path != '/login')
			{
				// store current route to reuse after login
				session_set('previous_path', get_path());
				redirect('/login');
			}
			// NO return required
		}
		
		public function down()
		{
			// NO return required
		}
	}
