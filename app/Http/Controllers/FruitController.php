<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */
	
	namespace Http\controllers;
	
	use core\FormRequests;
	use core\Request;
	use Http\Models\Fruit;
	use lib\db\mysqliDB;
	
	// validator used in controller.
	class FruitController
	{
		private $dbClass;
		public $failMessages=[];
		
		public function __construct()
		{
			$this->dbClass = new mysqliDB();
			// dd( lib\db\pdoDB::query('select * from `fruit`')); // example usage of PDO
		}
		
		public function index(Request $request, $bla=null, $bla2=null)
		{
			$this->data = (new Fruit())->select()->orderby('name')->get();
			$this->useView='fruit.index';
		}
		
		public function index2(Fruit $fruit)
		{
			header('Content-Type: application/json; charset=utf-8');
			header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Token-Auth');
			$middlewareParams = ['middleWareParamsFromRoute' => response('params MiddleWare given from Route-file')];
			response_set('params MiddleWare given from Route-file', null);
			$fruits           = ['requestDataFromModelSql' =>  $fruit->all()];
			$dataObject       = (object) array_merge($middlewareParams , $fruits);
		
			echo json_encode($dataObject, JSON_PRETTY_PRINT);
			die;
		}
		
		public function add(Request $request, FormRequests $validator)      // core\Request $request
		{
			$request->all();

			if(isset($request->obj->post->submit))
			{
				$validator->validator($request->obj->post, 'fruit'); // call FruitRequest for validation
				if(!is_array($validator->fails))
				{
					$data = $request->getFillable(['name', 'color', 'sweetness'], true);
					if($this->dbClass->querySQL('INSERT INTO `fruits` (`name`, `color`, `sweetness`) VALUES ('.$data.')'))
					{
						$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record inserted with the name: <i><b>'
							.$request->obj->post->name.'</b></i> in \'Fruits\''];
						redirect("/fruits", $message);   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages=(object)$validator->fails['fail'];  // push validation-errors to view
					$this->populate = $request->obj->post;
					back();
				}
			}
			$this->useView='fruit.add';
		}
		
		public function update(Request $request, FormRequests $validator, $id)
		{
			$request->all();
			if(isset($request->obj->put->submit))
			{ //  submitted, chack validation-form and sql-update
				$validator->validator($request->obj->put, 'fruit'); // call FruitRequest for data-validation

				if(!is_array($validator->fails))        {
					$data=$request->getFillable(['name', 'color', 'sweetness']);
					if($this->dbClass->querySQL('UPDATE `fruits` SET
					                            `name` = "'.$data->name.'",
					                            `color`= "'.$data->color.'" ,
					                            `sweetness`= "'.$data->sweetness.'"
												WHERE `id` = '.$id, true)) // sweetneess must be int.
					{
						$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record to update with the name: <i><b>'
							.$request->obj->put->name.'</b></i> is updated in \'Fruits\''];
						redirect("/fruits", $message);   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages = (object) $validator->fails['fail'];  // push validation-errors to view
					$this->populate = $request->obj->put;
					redirect("/fruits");   // redirect
				}
			}
			elseif(empty($_POST))
			{            // geen submit, dan select=sql --> gekregen waarden in POST zetten
				$this->populate = $this->dbClass->querySQL('SELECT * FROM `fruits` WHERE `id` ='.$id);
				$this->id       = $this->populate->id;
			}
			$this->useView='fruit.update';
		}
		
		public function delete(Request $request, $id)
		{
			$request->all();
			if(is_numeric($id))
			{
				$result = $this->dbClass->querySQL('DELETE FROM `fruits` WHERE `id` = "'.$id.'"'
						. ' AND EXISTS (SELECT count(`id`) FROM `fruits` WHERE `id` = "'.$id.'")');
				if($result == true)     {
					$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record with id: <i>'.$id.'</i> is deleted from \'Fruits\''];
					redirect("/fruits", $message);                // redirect
					//header("Location: ".url("/fruits"));   // redirect
				}
				elseif($result == false )    {
					$message = ['type'=>'warning', 'strong'=>'Warning!', 'message'=>'Record with id: <i>'.$id.'</i> is NOT deleted from \'Fruits\''];
					redirect("/fruits", $message);                // redirect
				}
			}
			$this->useView='fruit.index';
		}
	}
