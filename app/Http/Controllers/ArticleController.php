<?php
	
	namespace Http\Controllers;
	/**
	 * Project: MVC2022
	 * Author:  InCubics
	 * Date:    20/12/2022
	 */
	class ArticleController
	{
		public function index()
		{
			// get data from database-table
			$this->data=[['id'=>"1", 'title'=>"Home", 'content'=>"Hello all"], ['id'=>"3", 'title'=>"Cool", 'content'=>"Coding is cool"], ['id'=>"7", 'title'=>"Party", 'content'=>"A finished <b>project</b> end"]];
			$this->title='All articles';
			$this->useView='articles.index';
		}
		
		public function show(\core\Request $request)
		{
			$this->id=$request->all()->get->p1; // param0 from url, eq: article/1
			$this->data=['apple', 'pear', 'lemon', 'peach'][$this->id];  // test array naar view
			$this->string="Hello show-view of articles";     // test string naar view
			$this->title='one article';
			$this->useView='articles.show';
		}
		
		public function add()
		{
			$this->useView='articles.add';        // view not created
		}
		
		public function update()
		{
			$this->useView='articles.edit';       // view not created
		}
		
		public function delete()
		{
			$this->useView='articles.delete';       // view not created
		}
	}
