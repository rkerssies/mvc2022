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
		
		public function index(Fruit $fruit){
				$this->bla2 = 'query with BindParams';  // if "bla" is passed by router, then it has a value. eq:    /fruit/value/sasa
		/* //////// some EXAMPLES with eloquent-alike data-queries    /////////// */
					//	dd((new Fruit())->find(25)->get()); // OK  finding id !
					//	dd(((new Fruit())->raw('SELECT * FROM `fruits` WHERE `id` = ?', ['id'=> 25]))->meta); // OK  finding id on RAW !
//					 dd((new Fruit())->select()->where('sweetness', 1)->get()); // ok
					/*dd((new Fruit())->select(['avg'=>'sweetness',
											'sum'=> 'sweetness',
											'min'=> 'sweetness',
											'max'=> 'sweetness'])->where('color', 'yellow')->get()); // ok*/
//					dd((new Fruit())->select(['color','sweetness'], true)->where('color', 'green')->get());

	//// relations
//		    dd((new User())->select()->oneOnMany('id','user_id','users_fruits')->get()); //OK
//	        dd((new User())->select()->manyOnMany('Fruit')->get());
			
			$this->data = (new Fruit())->select()->all()->pagination(5)->get();
			
					//	dd((new Fruit())->all()->limit(3,4)->get()); // OK all
					//	dd((new Fruit())->all()->get()); // OK all
					//	dd((new Fruit())->all()->toJson()->get()); // OK all
		/* ///////////////////////////////////////////////////////////////////// */
//			$this->data = (new Fruit())->select()->orderby(['name', 'color'])->get(); // ok orderBy
			$this->useView='fruity.index';
		}
		
		public function add(Fruit $fruit, Request $request, FruitRequest $validator)      // core\Request $request
		{
			$request->all();
			if(isset($request->post->submit))
			{
				$validator->validator($request->post, 'fruit'); // call FruitRequest for validation
				if(!is_array($validator->fails))                // dd($validator->fails);
				{
					if($fruit->insert($request->getFillable(['name', 'color', 'sweetness'])))
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
			$request->all();
			if(isset($request->post->submit))
			{ //  submitted, chack validation-form and sql-update
				$validator->validator($request->post, 'fruit'); // call FruitRequest for data-validation
				if(empty($validator->fails))    {
					$fruit->update($request->getFillable(['name', 'color', 'sweetness']), $id);
					if($fruit->affected_rows > 0)    {
							// set info messagebar after redirect
						$message = ['type'=>'success', 'strong'=>'Success!', 'message'=>'Record with the name: <i><b>'
							.$request->post->name.'</b></i> is updated in \'Fruits\''];
						redirect("/fruity", $message);   // redirect
					}
				}
				else    {   // validation failed
					$this->failMessages = (object)$validator->fails['fail'];  // push validation-errors to view
					back();
				}
			}
			elseif(empty($request->post))
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
