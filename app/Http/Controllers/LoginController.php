<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */

	namespace Http\Controllers;

	use core\Request;
	use Http\Models\User;
	use Http\Validation\LoginRequest;
	use Http\validation\NewpassRequest;
	use lib\mail\Smtp;

	class LoginController extends \stdClass
	{
		public function show(LoginRequest $validator, Request $request, User $user) //
		{
			if($request->all()->post->logoff)
			{
				session_destroy();
				redirect('/');
			}
			elseif($request->all()->post->submit)
			{
				$validator->validator($request->post, 'login'); // call FruitRequest for validation
				if(!is_array($validator->fails))    {
					//TODO check for validation on login-form
				}
				$userFound = $user->select()
					->where('username', $request->all()->post->username)
					->andWhere('password', sha1($request->all()->post->password))
					->get(['id','username', 'profile', 'password']); // password is hidden when using models
				if(isset($userFound->username))
				{
					session_set('login', (array)$userFound);
					if(session_get('previous_path'))
					{
						$redirect=session_get('previous_path')->scalar;
						if(empty($redirect))
						{
							$redirect='/';
						}
						redirect($redirect); // scalar: converting object to string
					}
					back();
				}
			}
			$this->useView='login.show';
		}

		public function forgotPass(Request $request, User $user)
		{
			$request->all();

			if(!empty($request->all()->post->username))
			{
				$this->userFound = $user->select()->where('username',$request->all()->post->username )->get();

				if( !empty($this->userFound->id ))
				{
					$this->hash = date('sY').md5($this->userFound->username).date('i');
					if((new User())->update(['forgot_hash'=> $this->hash], $this->userFound->id))   // default search on ID
					{
						$mail=  (new Smtp('forgot_password', 'Renewing password'))
							->sendSmtp($this->userFound->username, ['hash'=> $this->hash, 'username'=>$this->userFound->username]);
						if(!is_bool($mail) && is_string($mail)) {
							dd('<p style="color:red;">eMail EXAMPLE >>></p><hr>'.$mail);  // show example
						}
						$this->msg = '<p style="color:blue;">Check your email and click the button to continue the password-renewal procedure</p>';
						$this->useView='login.show';
					}
				}
				else {
					$this->msg = '<p style="color:red;">invalid username</p>';
					// no population on login-form
					$this->useView='login.forgot';
				}
			}
			else {
				$this->useView='login.forgot';
			}

		}

		public function changePass(Request $request, NewpassRequest $validator, User $user)
		{
			///// // renew password for user with valid account
			if(!empty(session_get('login')) )
			{
				$this->id = session_get('login')->id;
				if(!empty($request->all()->post->submit))
				{
					$validator->validator($request->all()->post, 'newpass'); // call FruitRequest for data-validation
					$hashedPass= sha1($request->all()->post->password1);
					if(empty($validator->fails)) {
						$hashedPass= sha1($request->all()->post->password2);
						$user->update(['password'=>$hashedPass], $this->id);
						header('Location: '.url('/'));
					}
				}
				else {  // validation failed
					$this->populate = $request;
					$this->failMessages = (object) $validator->fails['fail'];  // push validation-errors to view
				}
				$this->useView  = 'login.renew'; // view with double new password + Request chack both the same
			}

			//// // renew password without login
			if(!empty($request->all()->get->p1)) { $this->hash = $request->all()->get->p1; }
			else {  $this->hash = ''; }
			$resultFind = $user->select()->where('forgot_hash', $this->hash)->get();
			$this->id = $resultFind->id;
			if(!empty($this->hash) && $resultFind == true)  {
				if(!empty($request->all()->post->submit))
				{   // submit received
					$this->id           = $request->all()->post->id;
					$validator->validator($request->all()->post, 'newpass'); // call FruitRequest for data-validation
					if(empty($validator->fails)) {
						$hashedPass     = sha1($request->all()->post->password2);
						(new User())->update(['forgot_hash'=> null, 'password'=>$hashedPass], $this->id); // !! new object
						header('Location: '.url('/'));
					}
					elseif(!empty($validator->fails) && !empty($request->post->id))    {  // validation failed
						$this->populate     = $request->all()->post;
						$this->failMessages = (object)$validator->fails['fail'];  // push validation-errors to view
					}
					elseif(empty($resultFind))
					{   // no valid Hash, login-session OR hash matched
						$this->msg      = '<p style="color:red;">An invalid or no hash was provided to renew password.<br>Submit a new request</p>';
						$this->useView  = 'login.forgot';
					}
				}
				$this->useView='login.renew'; // view with double new password + Request chack both the same
			}
		}
	}
