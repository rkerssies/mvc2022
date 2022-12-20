<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    01/07/2022
	 * File:    FruitRequest.php
	 */
	

	namespace Http\validation;
	
	use core\FormRequests;
	
	class FruitRequest extends FormRequests
	{
		public function rules()
		{
			return [
				'name'      => 'required|min:4',
				'color'     => 'required',
				'sweetness' => 'required'  //|numeric
			];
		}
 	
	}

