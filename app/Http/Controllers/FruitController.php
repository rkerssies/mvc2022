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
			$this->bla2=$bla2;  // if "bla" is passed by router, then it has a value. eq:    /fruit/value/sasa
			//			dd($request->all());        // example to see all request-data with Request-instance
			$this->data = $this->dbClass->querySQL('SELECT * FROM `fruits` ORDER BY `name`');
			$this->useView='fruit.index';
		}
		
		public function index2(Fruit $fruit)
		{
			// usage of the Fruit-model with basic functionality Eloquent alike.
			dd("data-dump on url-path: /fruit2<br> with route-Middlweare 'rbac' thah has params <br>".json_encode($fruit->all()));
		}
		
		public function add(Request $request, FormRequests $validator)      // core\Request $request
		{
			$request->all();
			if(isset($request->post->submit))
			{
				$validator->validator($request->post, 'fruit'); // call FruitRequest for validation
				//dd($validator->fails);
				if(!is_array($validator->fails))
				{
					$data=$request->getFillable(['name', 'color', 'sweetness'], true);
					if($this->dbClass->querySQL('INSERT INTO `fruits` (`name`, `color`, `sweetness`) VALUES ('.$data.')'))
					{
						redirect("/fruits");   // redirect
					}
				}
				else
				{   // validation failed
					$this->failMessages=(object)$validator->fails['fail'];  // push validation-errors to view
				}
			}
			$this->useView='fruit.add';
		}
		
		public function update(Request $request, FormRequests $validator, $id)
		{
			$request->all();
	
			if(isset($request->post->submit))
			{ //  submitted, chack validation-form and sql-update
				$validator->validator($request->post, 'fruit'); // call FruitRequest for data-validation
				if(!is_array($validator->fails))
				{
					$data=$request->getFillable(['name', 'color', 'sweetness']);
					if($this->dbClass->querySQL('UPDATE `fruits` SET
					                            `name` = "'.$data->name.'",
					                            `color`= "'.$data->color.'" ,
					                            `sweetness`= "'.$data->sweetness.'"
												WHERE `id` = '.$id, true)) // sweetneess must be int.
					{
						redirect("/fruits");   // redirect
					}
				}
				else
				{   // validation failed
					$this->failMessages=(object)$validator->fails['fail'];  // push validation-errors to view
				}
			}
			elseif(empty($_POST))
			{            // geen submit, dan select=sql --> gekregen waarden in POST zetten
				$this->populate=$this->dbClass->querySQL('SELECT * FROM `fruits` WHERE `id` ='.$id);
				$this->id=$this->populate->id;
			}
			$this->useView='fruit.update';
		}
		
		public function delete(Request $request, $id)
		{
			$request->all();
			if(is_numeric($id) && $this->dbClass->querySQL('DELETE FROM `fruits` WHERE `id` = '.$id))
			{
				redirect("/fruits/var_value3/var_value4");      // redirect
				//header("Location: ".url("/fruits/var_value3/var_value4"));   // redirect
			}
			else
			{
				echo 'id <b>'.$request->get->p1.'</b> doesn\'t exist';
			}
		}
	}
