<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    Fruit.php
	 */
	
	namespace Http\Models;
	
	class Fruit extends Model // OR for easier usage: ModelBasic
	{
		//protected $table = 'other_tablename_than_fruits';        /* default: tablename = modelname in small-case appended with an 's' */
		protected $fillables    = ['name', 'color', 'sweetness'];
//		protected $hidden       = []; // eq: 'sweetness', 'minSweetness'
//		protected $primary      = 'somePrimatyKeyId';
		public $inserted_id     = null;
		public $affected_rows     = null;
		public $queryString     = null;
		
		public function getFillables()
		{
			return $this->fillables;
		}
		
		public function fruitsOrdered()     // models may contain methods to create callable queries for reuse
		{
			return $this->select()->orderby('name')->get();
		}
	}
