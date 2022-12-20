<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    Fruit.php
	 */
	
	namespace Http\Models;
	
	class Fruit extends Model
	{
		//protected $table = 'other_tablename_than "fruits", default tablename = modelname in small-case appended with an 's' ;
		protected $fillables    = ['name', 'color','sweetness'];
		
		
		// models may contain methods to create callable queries for reuse
		public function fruitsOrdered()
		{
			return $this->select()->orderby('name')->get();
		}
	}
