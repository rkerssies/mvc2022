<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 * File: _redirect_url.php
	 */
	
	function url( $path='')
	{
			if( isset($_SERVER['HTTPS'] ) ){
				$out = 'https://';
			}
			else {
				$out = 'http://';
			}
			$out.= rtrim($_SERVER['SERVER_NAME'],'/').'/';
			if(CONFIG['base_path'] != '/'){
				$out .= rtrim(ltrim( rtrim(CONFIG['base_path'],'/'),'/'),'/').'/';
			}
			$out .= ltrim($path, './');

			return $out;
	}
	
	function redirect($path = '/')
	{
		header('Location: '.url($path));
	}
	
	function back()
	{
		return $_SERVER['HTTP_REFERER'];
	}
	
	function get_path()
	{
		return ( str_replace(rtrim(CONFIG['base_path'],'/'), '', rtrim($_SERVER['REQUEST_URI'],'/')) );
	}
	
	function error( $statusCode = '404')
	{
		if(!is_numeric($statusCode) && $statusCode < 100  && $statusCode >= 600)   {
			die('Invalid status-numer (INT): '.$statusCode);
		}

		if(!file_exists('errorpages/'.$statusCode.'.html'))
		{
			die('File for error-page doesn\'t exist: '.'/public/errorpages/'.$statusCode.'.html');
		}
		header('Location: '.url('/errorpages/'.$statusCode.'.html'));
	}
