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
		public function __construct(string $templateName, $subject, $from=null)
		{
		
//			dd( SMTP );
			$this->mail = new PHPMailer();
			
			$this->mail->isSMTP();
			$this->mail->SMTPSecure = SMTP['SMTPSecure'];
			$this->mail->Host       = SMTP['host'];
			$this->mail->SMTPAuth   = SMTP['SMTPAuth'];
			$this->mail->Port       = SMTP['port'];
			$this->mail->Username   = SMTP['user'];
			$this->mail->Password   = SMTP['pass'];
			
			$this->templateName = $templateName;
			$this->subject      = $subject;
			
			if(!empty($from))   {
				$this->from       = SMTP['defaultFrom'];
				$this->fromName   = SMTP['defaultFromName'];
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
			
			if(SMTP['preview'] == false) {
				if($this->mail->send())   {
					return true;            // email send
				}
				return  false;
			}
			return $this->mail->Body;        // open emailBody in new browser-tab
		}
	}
