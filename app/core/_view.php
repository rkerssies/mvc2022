<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    30/03/2023
	 * File:    _view.phtml
	 */
	
	function yieldView(string $pathView)
	{
		/*
			implement a view into a layout, bypassing the controller and MVC-process.
			Used for showing eq: a messagebar-view placed (optional) in the layout
		*/
		
		if(!empty ($pathView))
		{
			$pathView=ltrim($pathView, '.');
			$viewPath = str_replace('.', '/', ltrim($pathView, '/'));
			if( ! file_exists('../app/views/'.$viewPath.'.phtml'))
			{
				die( 'Could not include view-file: ../app/views/'.$viewPath.'.phtml');
			}
			include('../app/views/'.$viewPath.'.phtml');
		}
		
	}
