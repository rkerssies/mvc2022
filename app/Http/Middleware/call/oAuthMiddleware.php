<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    04/04/2023
	 * File:    oAuthMiddleware.php
	 */
	
	namespace Http\Middleware\call;
	use Http\Controllers\Api\ApiResponses;
	use Http\Models\User;
	
	class oAuthMiddleware
	{
		// NB: more separate MiddleWare-classes can be made in the ./middleware - folder
		public function up()
		{
			$token = null;
			if(isset(apache_request_headers()['token'])){
				$token = apache_request_headers()['token'];
			}
			elseif(!empty(request()->get->token))   {   // token in url with param 'token', eq: index.php?token=a1b2c3d4
				$token = request()->get->token;
			}
			
			if(!empty($token)) {
				$user = new User();
				$result = $user->select(['id', 'username', 'profile'])->where('token', $token )->get();
				response_set('tokenUser', $result);     // found user in response for usage eq: RBAC
			}
			
			$responseObj =new ApiResponses();
			if(empty($token) ){
				$responseObj->status = 403;
				$responseObj->message = "Unauthicated ! No token is provided in header or get-param with the name 'token'";
				$responseObj->sendResponse();
			}
			elseif($user->num_rows != 1 ){
				$responseObj->status = 403;
				$responseObj->message = "Unauthicated ! No valid token is provided in header or get-param with the name 'token'";
				$responseObj->sendResponse();
			}
			
			// NO return required
		}
		
		public function down()
		{
			// NO return required
		}
	}
