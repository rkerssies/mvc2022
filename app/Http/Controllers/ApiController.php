<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    04/04/2023
	 * File:    ApiController.php
	 */
	
	namespace Http\Controllers;
	
	use Http\Controllers\Api\ApiResponses;
	use core\Request;
	use Http\Models\User;
	use Http\Validation\UserRequest;
	
	class ApiController extends ApiResponses
	{
		public $model ;
		public function __construct()
		{
			$this->model = strtolower(response()->requestParams->model);
			$method = strtolower(request()->method);
			$this->method   = $method;
			
			$this->nsModel = 'Http\\Models\\'.ucfirst($this->model);
			if(request()->get->p1 != 'token' && !class_exists($this->nsModel) && request()->get->p0 != 'api')
			{
				$this->message  = 'Requested Model unknown: '.$this->nsModel ;
				$this->sendResponse();
			}
			if(request()->get->p1 != 'token')
			{       // path /token doens't sreact for a peticular model in url ( $model )
				$this->total = (int) (new $this->nsModel())->select(['count'=>'id'])->get()->countId;
				$this->Model = new $this->nsModel();
			}
			
			// setting response-data get && choice of post, put, patch or delete
			$this->requestData = (object) [];
			$this->requestData->get = request()->get;
			if($method != 'get'){
				$this->requestData->$method = request()->$method;
			}
		}
		
		//TODO if RBAC on route , then check if valid!
		//TODO check on rolles and permissions with RBAC
		
		public function all()
		{
			if(!empty(request()->get->page)) {
				$page = (request()->get->page - 1);
				$amount = CONFIG['pagination']['amount'];
				$from = ($page * $amount);
				$this->data = $this->Model->select()->limit($from, $amount)->get();
			}
			else {
				$page   = null;
				$amount = null;
				$this->data = $this->Model->all()->get();
			}
			
			if(!empty($this->data))
			{
				$this->success  = true;
				$this->status   = 200;
				$this->message  = 'All records of Model: '.ucfirst(request()->get->p1);
				$this->count    = count($this->data);
			}
			else {
				$this->success  = true;
				$this->status   = 200;
				$this->message  = 'No records found of Model: '.ucfirst($this->method).'. ';
				if(!empty($page)) {
					$this->message  .= 'Paginated page: '.$page.' and max '.$amount.' per page';
				}
			}
			$this->paginate = (int) $amount;
			$this->page     = ($page+1);
			$this->sendResponse();
		}
		public function find()
		{
			$this->data = $this->Model->find(request()->get->p2)->get();
			if(!emptY($this->data))
			{
				
				$this->success  = true;
				$this->status   = 200;
				$this->message  = 'Record found in Model: '.ucfirst(request()->get->p1).' with id: '.request()->get->p2;
				$this->count    = 1;
			}
			else {
				$this->data     = false;
				$this->status   = 400;
				$this->message  = 'Record NOT FOUND in Model: '.ucfirst(request()->get->p1).' with id: '.request()->get->p2;
			}
			$this->sendResponse();
		}
		public function first()
		{
			if($this->data = $this->Model->all()->limit(0,1)->get())
			{
				$this->success  = true;
				$this->status   = 200;
				$this->message  = 'First record found in Model: '.ucfirst($this->model);
				$this->count    = 1;
			}
			$this->sendResponse();
		}
		
		public function add()
		{   // inserts just one record (not multiple)

			$request = new Request();

			$nsrequest = 'Http\Validation\\'.ucfirst($this->model).'Request';
			$validator = new $nsrequest();
			$validator->validator(request()->post, strtolower($this->model)); // call FruitRequest for data-validation
			//	$validator->validator(request()->post, strtolower(response()->requestParams->model));   // alternative solution, without $request-object
			if(empty($validator->fails))
			{
				if(empty($this->Model->getFillables()))
				{
					$this->message = 'No method getFillables in requested Model: '.ucfirst($this->model);
				}
				// if($this->Model->insert(request()->getFillable($this->Model->getFillables())))  // alternative solution, without $request-object
				if($this->Model->insert($request->getFillable($this->Model->getFillables())))
				{   $this->data         = true;
					$this->success      = true;
					$this->status       = 201;
					$this->message      = 'Record inserted into Model: '.ucfirst($this->model);

					$this->affected     = 1;
					$this->total        = ($this->total + 1);
					$this->inserted_id  = $this->Model->inserted_id; ///
					$this->sendResponse();
				}
			}
			$this->data         = false;
			$this->status       = 400;
			$this->message      = 'No record inserted into Model: '.ucfirst($this->model);
			$this->validation   = (object) $validator->fails;
			$this->sendResponse();
		}
		
		public function delete()
		{
			$result = $this->Model->delete(request()->delete->id);
			if($result == true && $this->Model->affected_rows == 1) {   // record found and deleted
				$this->data     = true;
				$this->success  = true;
				$this->status   = 201;
				$this->message  ='Record deleted from Model: '.ucfirst($this->model).' with id: '.request()->get->p3;
				$this->affected = 1;
				$this->total    = ($this->total - 1);
			}
			elseif($result == true && $this->Model->affected_rows == -1)  {     // non existing record to delete
				$this->data     = false;
				$this->success  = false;
				$this->status   = 400;
				$this->message  ='No record found to deleted on Model: '.ucfirst($this->model).' with id: '.request()->get->p3;
				$this->affected = 0;
			}
			else    {
				$this->data      = false;
				$this->status    = 400;
				$this->message   = 'No record deleted from Model: '.ucfirst($this->model).' with id: '.request()->get->p3;
			}
			$this->sendResponse();
		}
		
		public function update()
		{   // updates just one record (not multiple)
			$request = new Request();

			$nsrequest = 'Http\Validation\\'.ucfirst($this->model).'Request';
			$validator = new $nsrequest();
			$validator->validator($request->all()->put, strtolower($this->model)); // call FruitRequest for data-validation

			if(!is_array($validator->fails))
			{
				if(empty($this->Model->getFillables())) {
					$this->message='No method getFillables in requested Model: '.ucfirst($this->model);
				}

				if($this->Model->update($request->getFillable($this->Model->getFillables()), request()->get->p3))
				{
					if($this->Model->affected_rows == -1)   {   // nothing changed; submitted-data corresponds with the stored-data
						$this->success      = true;
						$this->status       = 200;
						$this->message      = 'No changes to update on record in Model: '.ucfirst($this->model).' with id: '.request()->get->p3;
					}
					else {  // update succeeded
						$this->data         = true;
						$this->success      = true;
						$this->status       = 201;
						$this->message      = 'Record updated in Model: '.ucfirst($this->model).' with id: '.request()->get->p3;
						$this->affected     = 1;
					}
					
					$this->sendResponse();
				}
			}
			$this->status       = 400;
			$this->message      = 'No record updated in Model: '.ucfirst($this->model).' with id: '.request()->get->p3;
			$this->validation   = (object) $validator->fails;
			$this->sendResponse();
		}
		
		
		
		////////////// TOKEN //////////////////////////////
		public function token()
		{
			$method = strtolower($this->method);
			$this->request = (object) [];
			$this->request->$method = request()->$method;
			if(!empty($requestedData->$method->password)) {
				$this->request->$method->password = '***hidden***';
			}
			$this->model = 'user';

			if(!empty(request()->post->username) && !empty(request()->post->password))
			{
				$validator = new UserRequest();
				$validator->validator(request()->post, strtolower('user')); // call FruitRequest for data-validation
				if(empty($validator->fail))
				{
					$model  = new User();
					$result = $model->select(['id', 'username', 'profile'])
						->where('username', request()->post->username)
						->andWhere('password', sha1(request()->post->password))
						->get();

					if($model->num_rows == 1)
					{
						$token=sha1(date('si').CONFIG['app_key'].'ToKeN'.date('lu'));
						$token.=sha1(date('Hi').CONFIG['app_key'].'InCubics'.date('is'));
						if((new User())->update(['token'=>$token], $result->id))
						{
							$this->data         ="JWT: ".substr($token, 0, 100);
							$this->success      = true;
							$this->requestData  = (object) [];
							$this->requestData->get  =  request()->get;
							$this->requestData->post =  request()->post;
							$this->requestData->post->password =  '***hidden***';
							$this->status       = 200;
							$this->count        = 1;
							$this->request      = $requestedData;
							$this->message      ='Token created and stored for user: '.$result->username;
							$this->affected     = 1;
		
							$this->sendResponse();
						}
					}
				}
			}
			$this->requestData       = (object) [];
			$this->requestData->get  =  request()->get;
			$this->requestData->post =  request()->post;
			$this->status            = 403;
			$this->message           = 'Unauthorized, invalid API-account provided';
			$this->validation        = (object) $validator->fails['fail'];
			$this->sendResponse();
		}
		
		public function __destruct() {
		//	return true;
		}
	}
