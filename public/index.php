<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    public\index.php
	 */

session_start();

//	error_reporting(-1);
//	ini_set( 'display_errors', 1 );
//error_reporting(E_ALL ^ E_WARNING);
	error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);

include('../app/core/Mvc.php');

( new core\Mvc() )->site();
?>

