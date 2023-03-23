<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    19/12/2022
	 * File:    make.php
	 */

	function appkeyGenerate() {

		include('app/lib/encrypt/Salt.php');
		$parts = parse_ini_file('app/config/config.ini');    // get config-data in constant-var
		define( 'CONFIG', $parts);  // config-constants available in whole framework
		$appkey = (new lib\encrypt\Salt())->generateAppKey(40);
		$appkey = str_replace('<br>', ' ', $appkey);
		echo "\t \e[32m".'created app_key: '.$appkey ."\033[0m  \n\r";
		echo "\t \e[32m"."copy and past this app_key in the config-file.\033[0m  \n\r";
	}

