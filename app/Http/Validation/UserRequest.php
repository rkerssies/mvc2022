<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    01/07/2022
	 * File:    FruitRequest.php
	 */
	

	namespace Http\Validation;
	
	use core\FormRequests;
	
	class UserRequest extends FormRequests
	{
		public function rules()
		{
			return [
				'username'  => 'required|min:4',
				'password'  => 'required',
			];
		}
 	
	}

