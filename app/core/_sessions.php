<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    _sessions.php
	 */
	
	function session_get(string $key = null)
	{   // name "session" is already taken by PHP itself
		if(empty($key))
		{
			return (object) $_SESSION;
		}
		else {
			if(empty($_SESSION[$key])){
				return false;
			}
			return (object) $_SESSION[$key];
		}
	}
	
	function session_set(string $key, $value=null)
	{
		if(is_array($value) || is_object($value))
		{
			foreach($value as $k => $v)     {
				$_SESSION[$key][$k] = $v;
			}
			return true;
		}
		elseif(is_string($key) && is_string($value) && $_SESSION[$key] = $value)    {
			return true;
		}
		elseif(is_string($key) && is_numeric($value) && $_SESSION[$key] = $value)    {
			return true;
		}
		elseif(is_string($key) && is_bool($value) && $_SESSION[$key] = $value)    {
			return true;
		}
		
		return false;
	}
	
	function session_isset(string $key = null)
	{
		if($key != null && !empty($_SESSION[$key]))   {
			return true;
		}
		return false;
	}
	
	function sessionkey_unset(string $key )
	{
		if($key != null && !empty($_SESSION[$key]))   {
			unset($_SESSION[$key]);
			return true;
		}
		return false;
	}
	
	function session_kill()
	{
		$_SESSION=[];
		return true;
	}
	

