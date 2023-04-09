<?php
	/**
	 * Project: PhpStorm.
	 * Author:  InCubics
	 * Date:    04/04/2023
	 * File:    ApiResponses.php
	 */
	namespace Http\Controllers\Api;
	
	use core\Request;
	
	class ApiResponses
	{
		protected $objModel = null;
	
		protected $validatedRequest = null;
		protected $data     = null;
		protected $requestData = null;
		protected $success  = false;
		public    $status   = 404;  //Not Found; https://en.wikipedia.org/wiki/List_of_HTTP_status_codes
		public    $message  = 'FAILED; something went wrong! ';
		protected $hidden   = null;
		protected $validation=null;
		protected $count    = 0;
		protected $total    = null;
		protected $inserted_id = null;
		protected $affected = 0;
		protected $paginate = null;
		protected $page     = null;
		protected $token    = null;

		
		public function sendResponse()
		{
			if(empty($this->model)) {
				$model = response('requestParams')->model;
				$this->model = $model;
			}
			
			if(class_exists('\App\Http\Resources\\'.ucfirst($model).'Resource'))    {
				$nsResource ='App\Http\Resources\\'.ucfirst($model).'Resource';
				$this->data = new $nsResource((object)$this->data);
			}

			///CORS
			header('Access-Control-Allow-Origin', '*');
			header('Access-Control-Allow-Methods', 'GET, POST, PUT, PATCH, DELETE, OPTIONS');
			header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Token-Auth');

			// RETURN DATA
			header('Content-Type: application/json; charset=utf-8');
			header('response_state:'.$this->status );
			header('creator: InCubics.net (c)'.date('Y').'-'.(date('Y')+1) );

			echo   json_encode(['data' => $this->data, 'request'=> $this->requestData, 'meta'=>$this->getMeta()],JSON_PRETTY_PRINT );
			die;
		}
		
		private function getMeta()
		{
			return ['success'       =>$this->success,          // success: true || false
					'status'        =>$this->status,           // html response-status
					'message'       =>(string) $this->message,          // readable message
					'model'         =>$this->model,
					//'hiddenkeys'    =>request()->hiddenfields,    // fields marked to be hidden in model
					'validation'    =>$this->validation,       // if invalid data is submitted
					'count'         =>$this->count,            // count of records requested
					'total'         =>$this->total,            // count of all posible records in request
					'affected'      =>$this->affected,         // count of records affected
					'inserted_id'   =>$this->inserted_id,      // last id on insert
					'paginate'      =>$this->paginate,
					'page'          =>$this->page,
					//'whoami'=>['email'=>(!empty($user->email)) ? $user->email : null, 'id'=>(!empty($user->id)) ? $user->id : null],    // Who Am I
					];
		}
	}
