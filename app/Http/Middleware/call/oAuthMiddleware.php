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
			$token = apache_request_headers()['token'];
			if(empty($token)){
				$token = request()->get->token;
			}
			
			$user = new User();
			$result = $user->select(['id', 'username', 'profile'])->where('token', $token )->get();
			response_set('tokenUser', $result);     // found user in response for usage eq: RBAC
			
			$responseObj =new ApiResponses();
			if(empty($token) || $user->num_rows != 1 ){
				$responseObj->status = 403;
				$responseObj->message = "Unauthicated ! No token or valid token is provided in header or get-param with the name 'token'";
				$responseObj->sendResponse();
			}
			
			// NO return required
		}
		
		public function down()
		{
			// NO return required
		}
	}
