<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    29/06/2022
	 * File:    Salt.php
	 */
	
	namespace lib\encrypt;
	class Salt
	{
		private $salt = null;
		private $ivalue;
		private $key;
		private $secretKey;
		
		public function __construct(string $privateKey = null)
		{
			//dd(hash_algos());       // shows a list of all hashing-algorithms
			$this->secretKey = hash( hash_algos()[52],str_pad(
					CONFIG['author'].'*'.CONFIG['app_key'],32, "^_", STR_PAD_BOTH));
			
			// ($this->generateAppKey(33)); // generate a new key og 33 chars based on the string in: app/config/.privateKey
			if($privateKey == null){
				if(file_exists('../app/config/.privateKey')) {
					$file = '../app/config/.privateKey';
				}
				elseif(file_exists('app/config/.privateKey')) { // for artibuild to generate an app_key
					$file = 'app/config/.privateKey';
				}
				$privateKey  = file_get_contents($file);
				if(strlen($privateKey) < 20) { die('Failed ! length privateKey in ../app/config/.privateKey must be longer than 20 char\'s ');}
			}
			$this->key = hash('sha256', $privateKey);
			$this->ivalue = substr(hash('sha256', $this->secretKey), 0, 16); // sha256 is hash_hmac_algo
		}
		
		
		public static function class() { // no cinstructor in Singleton
			
			if (!self::$instance) {
				self::$instance = new self();    // or    __CLASS__
			}
			return self::$instance;
		}
		
		public function encryptSalt(string $string, string $encryptMethod="AES-256-CBC") // method key :23
		{
			if(! in_array(strtolower($encryptMethod), openssl_get_cipher_methods()))
			{       // all possible encryptionMethods:       openssl_get_cipher_methods()
				die('Failed ! incorrect encryptionMethod');
			}
			
			$result = openssl_encrypt($string, $encryptMethod, $this->key, 0, $this->ivalue);
			return base64_encode($result);  // output is a encripted value
		}
		
		public function decryptSalt($stringEncrypt= null, string $encryptMethod="AES-256-CBC")
		{
			if(! in_array(strtolower($encryptMethod), openssl_get_cipher_methods()))
			{       	// all possible encryptionMethods:       openssl_get_cipher_methods()
				die('Failed ! incorrect encryptionMethod');
			}
		
			return openssl_decrypt(base64_decode($stringEncrypt), $encryptMethod, $this->key, 0, $this->ivalue);
		}
		
		
		public function generateAppKey($n = 30)
		{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			
			for ($i = 0; $i < $n; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}
			if(file_exists('../app/config/.privateKey')) {
				$file = '../app/config/.privateKey';
			}
			elseif(file_exists('app/config/.privateKey')) { // for artibuild to generate an app_key
				$file = 'app/config/.privateKey';
			}
			$myfile = fopen($file, "w");     // !!!   read-write RIGHTS on file

			fwrite($myfile, $randomString);
			if(fclose($myfile))
			{
				return 'privateKey stored in file:  ./app/config/.privateKey<br>'.$randomString;
			}
			return false;
		}
		
		public function generateSecret($n = 30)
		{
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$randomString = '';
			for ($i = 0; $i < $n; $i++) {
				$index = rand(0, strlen($characters) - 1);
				$randomString .= $characters[$index];
			}
			return $randomString;
		}
	}
