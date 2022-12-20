<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    01/07/2022
	 * File:    FruitRequest.php
	 */
	

	namespace Http\validation;
	
	use core\FormRequests;
	
	class LoginRequest extends FormRequests
	{
		public function rules()
		{
			return [
				'username'      => 'required|email',            // check should be more advanced
				'password'      => 'required|passwordsimpel'     // check should be more advanced
			];
		}
 	
	}

