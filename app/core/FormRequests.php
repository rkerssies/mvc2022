<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    01/07/2022
	 * File:    FormRequests.php
	 */
	
	namespace core;
	
	use lib\validation\ValidationPatterns;
	
	class FormRequests  extends ValidationPatterns
	{
		public $fail = null;
		
		public function validator($request, string $item)
		{
			$fR = 'Http\Validation\\'.ucfirst($item).'Request';
			$rules = (new $fR())->rules();
			
			foreach($rules as $fieldName => $validationString)
			{
				$itemValidationArray = explode('|', $validationString);
				
				foreach($itemValidationArray as $v_item)
				{
					if($v_item == 'nullable' && empty(request()->$fieldName) ) {  // nullable
						break;
					}
					$v_item_array = explode(':', $v_item);
					
					if(!empty($v_item_array[1])) { $v_item_value = $v_item_array[1];}
					else { $v_item_value = null; }
					
					$validator_methodName = 'is_'.ucfirst($v_item_array[0]);
					if(! $this->$validator_methodName($request->$fieldName, $v_item_value))
					{
						$fails[$fieldName][]  = $fieldName.'-'.$this->failMessage;
						$this->fails = ['message' => 'submitted form has validation errors', 'status' => false, 'fail'=> $fails];
					}
				}
			}

			if(!empty($fails))  {
				return false;
			}
			return true;
		}
	}
