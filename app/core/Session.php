<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    09/07/2022
	 * File:    Session.php
	 */
	
	namespace core;
	
	use lib\encrypt\Salt;
	
	class Session
	{
		private $salt   = null;
		public function __construct()
		{

			/* Force the creation of a sessionid and a fingerprint of valid user */
			if(!$_SESSION || !isset($_SESSION['finger']))
			{
				$session = [];
				session_regenerate_id(true);
				$session['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
				$session['timestamp']       = date('Y-m-d H:i:s');
				if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))    {      /* Test for a proxy */
					$session['REMOTE_ADDR'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
				}
				else {
					$session['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
				}
				$saltObject = new Salt();
				$_SESSION['finger'] = $saltObject->encryptSalt(json_encode( $session));
			}
		}
		
		public function run()
		{   // check stored fingerprint
			$saltObject = new Salt();
			$session = json_decode($saltObject->decryptSalt($_SESSION['finger']));
			if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))    {      /* Test for a proxy visitor */
				$sessionAdrr = $_SERVER['HTTP_X_FORWARDED_FOR'];    //can contain client IP-address
			}
			else {
				$sessionAdrr = $_SERVER['REMOTE_ADDR'];             // contains client IP-address
			}
			
			if($sessionAdrr != $session->REMOTE_ADDR             // check on same ip
				|| $_SERVER['HTTP_USER_AGENT'] != $session->HTTP_USER_AGENT ) {   // check on same browser
				//options: adding register and bann ip-address.  // $_SERVER['REMOTE_ADDR']
				$_SESSION = array();
				session_destroy();
				 error('401'); // exit();
			}
			return true;
		}
	}
