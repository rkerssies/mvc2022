<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    19/12/2022
	 * File:    make.php
	 */

	function dbRefresh($argsArray) {
		
		$config = parse_ini_file("app/config/config.ini");

		$conn = mysqli_connect('127.0.0.1', $config['db']['user'], $config['db']['pass'], $config['db']['dbname']);
		if (mysqli_connect_errno()) {
			echo "Failed to connect to MySQL: " . mysqli_connect_error();
			exit();
		}
		
			$sqlDropDB = 'DROP DATABASE IF EXISTS `'.$config['db']['dbname'].'`';
			if(mysqli_query($conn, $sqlDropDB))
			{
				$sqlCreateDB = 'CREATE DATABASE `'.$config['db']['dbname'].'`';
				if(mysqli_query($conn, $sqlCreateDB))
				{
					$firstSqlFilePath = glob("*.sql")[0];
					$dsn = 'mysql:dbname='.$config['db']['dbname'].';host=127.0.0.1';
					$db = new PDO($dsn, $config['db']['user'], $config['db']['pass']);
					$sql = file_get_contents($firstSqlFilePath);
					$qr = $db->exec($sql);
					echo "\t \e[32m".'Database tables and data REFRESHED by sql-file'."\033[0m  \n\r";
				}
			}
			else {
				echo "\t \e[32m".'Something went wrong! NO REFRESHED database by sql-file'."\033[0m  \n\r";
			}
	}
	

