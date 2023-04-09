<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */
	
	namespace Http\Controllers;
	
	use core\Request;
	use Http\Models\Fruit;
	use Http\Validation\FruitRequest;
	use Http\Models\User;       // used in data-query examples in index-method
	
	use lib\mail\Smtp;
	
	class Fruit2Controller
	{
		private $dbClass;
		public $failMessages=[];
		
		public function __construct()
		{
			//
		}
		
		public function index(Fruit $fruit)
		{
			$this->data = (new Fruit())->select()->all()->pagination(5)->get(); // paginate 5 records per page, override config.ini setting
			$this->meta = (object) ['keywords'=> 'fruity, index, example, paginated, overview', 'description' => 'CRUD overview of paginated records in the fruity database-table.'];
			$this->useView='fruity.index';
		}
		
		public function add(Fruit $fruit, Request $request, FruitRequest $validator)      // core\Request $request
		{
			if(isset($request->all()->post->submit))
			{
				$validator->validator($request->all()->post, 'fruit'); // call FruitRequest for validation
				if(!is_array($validator->fails))   {                        // validation succes
					if($fruit->insert($request->all()->getFillable(['name', 'color', 'sweetness'])))    {
						$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record added to \'Fruits\''];
						redirect("/fruity", $message);   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages = (object)$validator->fails['fail'];  // push validation-errors to view
					$this->populate     = request()->post;
				}
			}
			$this->meta = (object) ['keywords'=> 'fruity, adding, example', 'description' => 'CRUD add-form with validation for the fruity database-table.'];
			$this->useView = 'fruity.add';
		}
		
		public function update(Fruit $fruit, Request $request, FruitRequest $validator, $id)
		{
			$method = strtolower(request()->method);    // possibility for: put, patch or post
			if(isset($request->all()->$method->submit))
			{ //  submitted, chack validation-form and sql-update
				$validator->validator($request->all()->$method, 'fruit'); // call FruitRequest for data-validation
				
				if(empty($validator->fails))    {
					$result = $fruit->update($request->all()->getFillable($fruit->getFillables()), $id);
					if($result == true && $fruit->affected_rows == -1)      {    // NOT EXISTING id
						$message = ['type'=>'warning', 'strong'=>'Warning!', 'message'=>'Record to update with id: <i>'
									.$id. '</i> in \'Fruits\' has NO changes'];
						redirect("/fruity", $message);   // redirect
					}
					elseif($result == true && $fruit->affected_rows == 1)    {
						// set info messagebar after redirect
						$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record to update with the name: <i><b>'
							.$request->$method->name.'</b></i> is updated in \'Fruits\''];
						redirect("/fruity", $message);   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages = (object) $validator->fails['fail'];  // push validation-errors to view
					$this->populate     = request()->$method;
					back();
				}
			}
			elseif(empty($request->$method))    {              // geen submit yet, query data and place in form-fields
				$this->populate = $fruit->find($id)->get();    // $id  == $request->get->p1
			}
			$this->meta = (object) ['keywords'=> 'fruity, updating, example', 'description' => 'CRUD update-form with validation for the fruity database-table.'];
			$this->useView = 'fruity.update';
		}
		
		public function delete(Fruit $fruit, $id)
		{
			if(is_numeric($id) && $result = $fruit->delete($id))
			{
				if($result == true && $fruit->affected_rows == -1)      {    // NOT EXISTING id
					$message = ['type'=>'warning', 'strong'=>'Warning!', 'message'=>'Record to delete with id: <i>'.$id. '</i> doen\'t exist in \'Fruits\''];
					redirect("/fruity", $message);   // redirect
				}
				elseif($result == true && $fruit->affected_rows == 1)  {    // DELETED
					$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record with id: <i>'.$id.'</i> is deleted from \'Fruits\''];
					redirect("/fruity", $message);   // redirect
				}
			}
			$this->meta = (object) ['keywords'=> 'fruity, deleting, example', 'description' => 'CRUD deleting-proces with no confirm-modal for the fruity database-table.'];
			$this->useView = 'fruity.index';
		}
	}
