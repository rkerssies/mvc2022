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
			if(env('app')->basepath != '/'){
				$out .= rtrim(ltrim( rtrim(env('app')->basepath,'/'),'/'),'/').'/';
			}
			$out .= ltrim($path, '/');
			return $out;
	}
	
	function redirect($path = '/', $messageBar = [])
	{
		if(!empty($messageBar) && is_array($messageBar)){
			$_SESSION['messagebar'] = $messageBar;
		}
		header('Location: '.url($path));
	}
	
	function back()
	{
		return $_SERVER['HTTP_REFERER'];
	}
	
	function get_path()
	{
		return ( str_replace(rtrim(env('app')->basepath,'/'), '', rtrim($_SERVER['REQUEST_URI'],'/')) );
	}
	
	
	function currentPath($qsa = false)
	{
			return $_SERVER['REQUEST_URI'];     // return complete path with qsa (eq: /fruit?page=3 )
	}
	
	function getQsa()
	{
		$pathParts = explode('?', $_SERVER['REQUEST_URI']);
		if(empty($pathParts[1]))    {
			return null;
		}
		return '?'.$pathParts[1];               // return only the sqa of an url-path (eq: ?page=3)
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
