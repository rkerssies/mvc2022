<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    18/03/2023
	 * File:    smpt.php
	 */
	namespace lib\mail;
	
	use vendor\PHPMailer\PHPMailer;
	
	class Smtp
	{
		public $preview = false;
		private $env;
		protected $mail;
		protected $templateName ='app/views/mail/template.phtml';
		protected $from;
		protected $fromName;
		protected $subject;

		public function __construct(string $templateName, $subject, $from=null)
		{
		
			$this->env = env('smtp');
			$this->mail = new PHPMailer();
			
			$this->mail->isSMTP();
			$this->mail->SMTPSecure = $this->env->encryption;  // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` also accepted
			$this->mail->Host       = $this->env->host;    
			$this->mail->SMTPAuth   = $this->env->auth;   	// Enable SMTP authentication
			$this->mail->Port       = $this->env->port;    	// TCP port to connect to];
			$this->mail->Username   = $this->env->username; 
			$this->mail->Password   = $this->env->password; 
			
			$this->templateName = $templateName;
			$this->subject      = $subject;
			
			if(!empty($from))   {
				$this->from       = $this->env->from;
				$this->fromName   = $this->env->fromName;
			}
			else    {
				$this->from       = $from;
				$this->fromName   = '<'.$from.'>';
			}
			$this->mail->setFrom($this->from, '<'.$this->fromName.'>');
			
		//	$this->mail->addAddress('address.to@example.com', 'Address to');
			
			
			$this->preview = true; // get mode from config.ini to send or display test-preview
		}
		
		public function sendSmtp(string $to, array $dataEmail=[], string $cc=null)
		{
			$this->mail->addAddress($to, '<'.$to.'>');
			if(!empty($cc)) {     // cc might be a comma seperated string with email-addresses
				$this->mail->AddCC($cc);
			}
			
			if( file_exists("../app/views/mail/".$this->templateName.'.phtml' )) {
				$this->mail->isHTML(true);
				ob_start(); // data in array : $dataEmail in inject into email
				
				$data = (object)array_merge($dataEmail, ['from'=> $this->from, 'fromName' => $this->fromName ]);
					include "../app/views/mail/".$this->templateName.'.phtml';
				$this->mail->Body = ob_get_clean();
			} else {
				die( 'Unable to find file with email-template: '."app/views/mail/".$this->templateName.'.phtml');
			}
			
			//$this->mail->AddAttachment($_FILES["attachment"]["tmp_name"], $_FILES["attachment"]["name"]);

			if($this->env->preview == false) {
				return $this->mail->Body;        // open emailBody in new browser-tab
			}
			if($this->mail->send())   {			// email send
					return true;            
			}
			return  false;
		}
	}
