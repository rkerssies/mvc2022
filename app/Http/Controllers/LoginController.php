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
	
	class LoginController
	{
		public function show(LoginRequest $validator, Request $request, User $user)
		{
			$request->all();
			if($request->post->logoff)
			{
				session_destroy();
				redirect('/');
			}
			elseif($request->post->submit)
			{
				//TODO check for validation !
				$validator->validator($request->post, 'login'); // call FruitRequest for validation
				if(!is_array($validator->fails))
				{
					//
				}
				$userFound=$user->select()
					->where('username', $request->post->username)
					->andWhere('password', sha1($request->post->password))
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

			if(!empty($request->post->username))
			{
				$this->userFound = $user->select()->where('username',$request->post->username )->get();
				if( !empty($this->userFound) && $user->num_rows == 1)
				{
					$this->hash = sha1(date('Hm').$user->values['username'].date('i')); // 40 chars long
					
					if((new User())->update(['forgot_hash'=> $this->hash], $this->userFound->id))   // default search on ID
					{
						if($mail=  (new Smtp('forgot_password', 'Renewing password'))
						->sendSmtp($this->userFound->username, ['hash'=> $this->hash, 'username'=>$this->userFound->username]))
						{
							dd('eMail EXAMPLE >>><br><hr><hr>'.$mail);  // show example
						}
					}
				}
				else {
					$this->msg = '<p style="color:red;">invalid username</p>';
				}
			}
			$this->useView='login.forgot';
		}
		
		public function changePass(Request $request, User $user)
		{
			$request->all();
			if($request->post->password1 != $request->post->password2)      // validation ???
			{
				back();
			}
			if(!empty($request->post->submit) && $request->post->password1 == $request->post->password2)
			{
				$user->update(['forgot_hash'=> '', 'password'=> sha1($request->post->password1) ], $request->post->id);   // default search on ID
				// set session for login ?
				header('Location: '.url('/'));
			}
			elseif(!empty($request->get->p1) && is_string($request->get->p1))       // p1 contains hash
			{
				if($userFound = $user->select()->where('forgot_hash' , $request->get->p1)->get()) // last update to long ago ??
				{
					$this->id = $userFound->id;
					$this->useView='login.renew';
				}
			}
			elseif(empty($request->post) && !empty(session_get('login')))
			{   // renew password for user with inloged account
				$this->hash = null; // var needed for url-part
				$this->id = session_get('login')->id;     // neded for hidden field
				$this->useView='login.renew'; // view with double new password + Request chack both the same
			}
			else {
				$this->msg = '<p style="color:red;">An invalid or no hash was provided to renew password.<br>Submit a new request</p>';
				$this->useView='login.forgot'; // view with double new password + Request chack both the same
			}
		}
	}
