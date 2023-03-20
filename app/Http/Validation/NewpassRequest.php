<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    01/07/2022
	 * File:    FruitRequest.php
	 */
	

	namespace Http\validation;
	
	use core\FormRequests;
	
	class NewpassRequest extends FormRequests
	{
		public function rules()
		{
			return [
				'password1'      => 'required|passwordsimpel',            // check should be more advanced
				'password2'      => 'required|passwordsimpel|same:password1'
			];
		}
 	
	}

