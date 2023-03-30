<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    18/08/2022
	 * File:    _response.php
	 */
	
	
	function response($key = null)
	{   // get key-value from response-object
		if(empty($key)) {
			return (new \core\Response())::class();
		}
		return \core\Response::class()->$key;
	}
	
	function response_set(string $key, $value)
	{
		if( \core\Response::class()->$key = $value)
		{
			return true;
		}
		return false;
	}
	
	function response_isset(string $key)
	{
		if( isset(\core\Response::class()->$key))
		{
			return true;
		}
		return false;
	}
	
	function response_toJson()
	{
		return \core\Response::class()->toJson();
	}
