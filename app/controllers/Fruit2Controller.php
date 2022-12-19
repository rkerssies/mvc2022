<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */
	
	use core\Request;
	use models\Fruit;
	use validation\FruitRequest;
	
	class Fruit2Controller
	{
		private $dbClass;
		public $failMessages = [];
		
		public function __construct()
		{
		
		}
		
		public function index(Fruit $fruit)
		{
			//$this->data = $fruit->all();
			
			$this->data = $fruit->select()->orderby('name')->get();

			$this->useView = 'fruity.index';
	
			
			// append meta-tag with custom values
			$this->meta = (object) ['keywords' => 'word1, word2, word3', 'description' => 'Bla bla bla describe...'];
		}
		
		public function add(Fruit $fruit, Request $request, FruitRequest $validator)      // core\Request $request
		{
			$request->all();
			if(isset($request->post->submit))
			{
				$validator->validator($request->post, 'fruit'); // call FruitRequest for validation
				if(!is_array($validator->fails))    			// dd($validator->fails);
				{
					if($fruit->insert($request->getFillable(['name', 'color', 'sweetness'])))   {
						redirect("/fruity");   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages = (object) $validator->fails['fail'];  // push validation-errors to view
				}
			}
			$this->useView = 'fruity.add';
		}

		public function update(Fruit $fruit, Request $request, FruitRequest $validator, $id)
		{
			$request->all();
			if(isset($request->post->submit))
			{ //  submitted, chack validation-form and sql-update
				$validator->validator($request->post, 'fruit'); // call FruitRequest for data-validation

				if(empty($validator->fails))    			// dd($validator->fails);
				{
					if($fruit->update($request->getFillable(['name', 'color', 'sweetness']), $id))   {
						redirect("/fruity");   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages = (object) $validator->fails['fail'];  // push validation-errors to view
					back();
				}

			}
			elseif(empty($request->post)){ 			// geen submit, dan select=sql --> gekregen waarden in POST zetten
				$this->populate = $fruit->find($id)->get();    // $id  == $request->get->p1
				// $this->populate = $fruit->find($id)->get(['id','name','color','sweetness']); // $id  == $request->get->p1
			}
			$this->useView = 'fruity.update';
		}

		public function delete(Fruit $fruit, $id)
		{
			if(is_numeric($id)
				&& $fruit->delete($id) )  {
				redirect("/fruity");
			}
			else {
				$this->arrayMessages =  [ ['info' => 'id <b>'.$id.'</b> doesn\'t exist']];
				$this->data = $fruit->select()->orderby('name')->get();
			}
			$this->useView = 'fruity.index';
		}
	}
