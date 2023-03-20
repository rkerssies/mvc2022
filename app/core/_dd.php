<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    _dd.php
	 */
	
	function dd($var=null)
	{
		echo '('.gettype($var).'):'."&emsp;";
		
		if(is_array($var) || is_object($var))
		{
			echo '<pre>';
			print_r($var);
			echo '</pre>';
			die;
		}
		elseif(is_numeric($var) )
		{
			echo $var;
			die;
		}
		elseif(is_bool($var) && $var === false )
		{
			die("false");
		}
		elseif(is_bool($var) && $var === true)
		{
			die("true");
		}
		elseif(empty($var))
		{
			die('NULL');
		}
		else
		{
			die($var);
		}
		die('dd - value of parameter: '.$var);
	}
	
	function generate_app_key()
	{
		//dd(hash_algos());       // list of all hashing algorithms
		return hash( hash_algos()[41], str_pad(substr(substr(sha1(CONFIG['domain']),0,-4).'*'.CONFIG['app_key'], 0, 32),
			32, "^_", STR_PAD_BOTH));
	}
