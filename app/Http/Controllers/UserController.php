<?php
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */

	namespace Http\controllers;

	use App\Models\User;
	use core\Request;
	use mysqliDB;

	class UserController extends \stdClass
	{
		public function __construct()
		{
			$this->dbClass=new mysqliDB();
		}

		public function index(User $user)
		{
			dd($user->all());
		}

		public function show(User $user, $id)
		{
			dd($user->find($id));
			//			 dd(request()->get->id);        // eq: user/12
		}

		public function add(User $user, Request $request)
		{
			//$user->insert($request->all());
		}

		public function update(Request $request)
		{
			//$user->update($request->all(), $id);
		}

		public function delete(Request $request)
		{
			//$user->delete($id);
		}
	}
