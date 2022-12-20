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
					->get(['username', 'profile', 'password']); // password is hidden when using models
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
	}
