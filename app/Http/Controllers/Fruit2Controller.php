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
			$this->useView='fruity.index';
		}
		
		public function add(Fruit $fruit, Request $request, FruitRequest $validator)      // core\Request $request
		{
			if(isset($request->all()->post->submit))
			{
				$validator->validator($request->all()->post, 'fruit'); // call FruitRequest for validation
				if(!is_array($validator->fails))                // dd($validator->fails);
				{
					if($fruit->insert($request->all()->getFillable(['name', 'color', 'sweetness'])))
					{
						$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record added to \'Fruits\''];
						redirect("/fruity", $message);   // redirect
					}
				}
				else
				{   // validation failed
					$this->failMessages = (object)$validator->fails['fail'];  // push validation-errors to view
				}
			}
			$this->useView = 'fruity.add';
		}
		
		public function update(Fruit $fruit, Request $request, FruitRequest $validator, $id)
		{
			$method = strtolower(request()->method);
			if(isset($request->all()->$method->submit))
			{ //  submitted, chack validation-form and sql-update
				$validator->validator($request->all()->$method, 'fruit'); // call FruitRequest for data-validation
				
				if(empty($validator->fail))    {
					$fruit->update($request->all()->getFillable(['name', 'color', 'sweetness']), $id);
					if($fruit->affected_rows > 0)    {
						// set info messagebar after redirect
						$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record with the name: <i><b>'
							.$request->post->name.'</b></i> is updated in \'Fruits\''];
						redirect("/fruity", $message);   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages = (object)$validator->failMessage;  // push validation-errors to view
					back();
				}
			}
			elseif(empty($request->$method))
			{            // geen submit, dan select=sql --> gekregen waarden in POST zetten
				$this->populate = $fruit->find($id)->get();    // $id  == $request->get->p1
				// $this->populate = $fruit->find($id)->get(['id','name','color','sweetness']); // $id  == $request->get->p1
			}
			$this->useView = 'fruity.update';
		}
		
		public function delete(Fruit $fruit, $id)
		{
			if(is_numeric($id) && $fruit->delete($id))  {
				$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record is deleted from \'Fruits\''];
				redirect("/fruity", $message);   // redirect
			}
			else    {
				$this->arrayMessages = [['info'=>'id <b>'.$id.'</b> doesn\'t exist']];
				$this->data = $fruit->select()->orderby('name')->get();
			}
			$this->useView = 'fruity.index';
		}
	}
