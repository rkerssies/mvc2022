<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    _request.php
	 */
	
	function request()
	{
		return	(new core\Request())->all();
	}
	
	function csrf()
	{
		return (new core\Request())->csrf();
	}
	
	function fillableArray(array $data= null, array $fillableFields = [])
	{
	  // select only fields that are listed as a fillable
		$i= 0;
		foreach($data as $key => $value)
		{
			if( is_array($value) || is_object($value))
			{   // multiple records with data-keys

				foreach($value as $k=>$v)
				{
					if(in_array($k, $fillableFields) || $k == 'id') {
						$arrayRecord[$k] = $v;
					}
				}
				$responseData[$i] = (object) $arrayRecord;
				$i++;
			}
			elseif(in_array($key, $fillableFields) || $key == 'id'){
				$responseData[$key] = $data[$key];
			}
		}
		return (object) $responseData;
	}
	
	function fillableString( array $fillableFields = [])
	{
		return	(new core\Request())->all()->getFillable((array) $fillableFields, true);
	}
	
	function populate(string $fieldName, $objPopulate = null) // TO USE IN VIEWS: get input-value to fill in input-value
	{
		
		$post = (object) request()->post;
		if($fieldName == 'id' && isset(request()->get->id) )   {
			return  request()->get->id;
		}
		if(!empty($post->$fieldName))
		{
			return (string) $post->$fieldName;
		}
		else {
			return (string) $objPopulate->$fieldName;
		}
		
	}
	

