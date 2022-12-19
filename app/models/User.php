<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    Fruit.php
	 */
	
	namespace models;
	
	class User extends Model
	{
		//protected $table = 'other_tablename_than "fruits"';
		protected $hidden       = ['password'];
		protected $fillables    = ['username', 'password','profile'];

	}
