<?php

	namespace Http\Controllers;
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */
	class ArticleController extends \stdClass
	{
		public function index()
		{
			// get data from database-table
			$this->data=[
							['id'=>"1", 'title'=>"Home", 'content'=>"Hello all"],
							['id'=>"3", 'title'=>"Cool", 'content'=>"Coding is cool"],
							['id'=>"7", 'title'=>"Party", 'content'=>"A finished <b>project</b> end"]
						];
			$this->meta = (object) ['keywords'=> 'atricle, home, example', 'description' => 'The homepage of MVC2022.'];
			$this->title='All articles';
			$this->useView='articles.index';
		}

		public function show(\core\Request $request)
		{
			$this->id=$request->all()->get->p1; // param0 from url, eq: article/1
			$this->data=['apple', 'pear', 'lemon', 'peach'][$this->id];  // test array naar view
			$this->string="Hello show-view of articles";     // test string naar view
			$this->title='One Article';
			if($this->id != 1) {
				$this->meta = (object) ['keywords'=> 'atricle, features, possibilities, screenshots, ',
					'description' => 'All features and possibilities of MVC2022 listed,
														with some screenshots to give an impression.'];
				$this->useView='articles.show2';
			}
			else {
				$this->meta = (object) ['keywords'=> 'atricle, features, Eloquent, MySqli, queries, API',
					'description' => 'Examples in querying data via MySqli and API.'];
				$this->useView='articles.show';
			}
		}

		public function add()
		{
			//TODO add logic to implement adding article-records
			$this->meta = (object) ['keywords'=> 'add, articles',
				'description' => 'Examples in querying data via MySqli and API.'];
			$this->useView='articles.add';        // view not created
		}

		public function update()
		{
			//TODO add logic to implement updating article-records
			$this->useView='articles.edit';       // view not created
		}

		public function delete()
		{
			//TODO add logic to implement deleting article-records
			$this->useView='articles.delete';       // view not created
		}
	}
