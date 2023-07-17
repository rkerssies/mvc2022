<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    lib\db\pdoDB.php
	 */

	namespace lib\db;

	use \PDO;

	class pdoDB extends \stdClass
	{
		private static $_pdo=null;

		private static function getDatabase($host = null, $user = null, $pass = null, $dbname = null)
		{
			if(self::$_pdo===null)
			{
				if(empty($host) && empty($user) && empty($pass) && empty($dbname) ) {
					$host = DB['host']; $user = DB['user']; $pass = DB['pass']; $dbname = DB['dbname'];
				}
				self::$_pdo=new PDO('mysql:host='.$host.';dbname='.$dbname , $user, $pass );
			}
			return self::$_pdo;
		}

		public static function query($query, $parameters=null)
		{
//			Database::_toArray($parameters);
			$query=self::getDatabase()
				->prepare($query);
			$query->execute($parameters);
			$result=$query->fetchAll(pdo::FETCH_ASSOC);
			return $result;
		}

		private static function _toArray(&$parameters)
		{
			if(!is_array($parameters))
			{
				$parameters=array($parameters);
			}
		}

		private function __construct()
		{
		}

		private function __clone()
		{
		}

		private function __wakeup()
		{
		}
	}
