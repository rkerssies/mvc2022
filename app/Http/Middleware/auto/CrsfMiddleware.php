<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    MyMiddleware.php
	 */
	
	namespace Http\middleware\auto;
	
	class CrsfMiddleware
	{
		public function up()
		{
			if( strtolower(request()->method) == 'post'
				|| strtolower(request()->method) == 'put'
				|| strtolower(request()->method) == 'patch')
			{
				request()->checkCsrf(); // continues or error-400
			}
			
			// NO return required
		}
		
		public function down()
		{
			unset($_SESSION['csrf']);
			
			// NO return required
		}
	}
