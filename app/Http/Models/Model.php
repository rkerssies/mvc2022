<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    lib\db\Model.php
	 */
	
	namespace Http\Models;
	
	use lib\db\mysqliDB;
	
	class Model
	{
		private $dbObject   = null;
		private $table      = null;
		public $queryString = null;
		
		public function __construct()
		{
			$class=  new \ReflectionClass($this);  // the Refelection-class several useful methods

			if(empty($this->table))     // get table from Model-name (default) or use table-name set in Model-class
			{   // table-names-format, small chars: model-name + s
				$array = explode('\\', $class->getName());
				$name = end($array);
				$this->table = strtolower($name.'s');
			}

			$this->dbObject = new mysqliDB();
		}
		
		
		/*
		 *      Methods that can be called in a specific Model because they inherit this Model-class with ist methods
		 */
		
		public function all()
		{
			return $this->dbObject->querySQL('SELECT * FROM `'.$this->table.'`');
		}
		
		public function find($id)
		{
			if(is_numeric($id))
			{
				$this->queryString = 'SELECT * FROM `'.$this->table.'` WHERE `id` = "'.$id.'" ';
			}
			else { $this->queryString = '<br>FAIL!  id "'.$id.'" is not a number <br>';}
			return $this;
		}
		
		public function get($arrayFieldnames = null)
		{   // this method is at the end of each method-chain on a Model (not find or all) to send the query to the server
			if(!$this->fillables) { die('Error: No fillables defined in model: '.ucfirst(rtrim($this->table, 's')));}
			
			$allData =  $this->dbObject->querySQL($this->queryString);
			
			if(isset($this->fillables) && is_array($arrayFieldnames))
			{
				foreach($arrayFieldnames as $selectedField)
				{
					if(!empty($this->hidden) && is_array($this->hidden)
						&& !in_array($selectedField, $this->hidden)
						&& in_array($selectedField, $this->fillables) ) { // show only NOT hidden fieldnames
						$dataResponse[$selectedField] = $allData->$selectedField;
					}
					elseif($this->hidden == false && in_array($selectedField, $this->fillables))
					{
						$dataResponse[$selectedField] = $allData->$selectedField;
					}
				}
				
				return (object) $dataResponse;
			}
			elseif(isset($this->fillables) && ! is_array($arrayFieldnames))    // no specific fields requetsed (return all fillables
			{
				return fillableArray((array) $allData, (array) $this->fillables);     // return only fillable fields defined in used Model.
			}
			return $allData;
		}
		
		//////////
		public function select($arrayFieldNames = '*')
		{
			if($arrayFieldNames != '*' && is_array($arrayFieldNames))   {
				$stringFieldnames ='';
				foreach($arrayFieldNames as $fieldName)
				{
					$stringFieldnames .= '`'.$fieldName.'`, ';
				}
				$fields = rtrim(', ',$stringFieldnames);
			}
			else    {
				$fields = '* ';
			}

			$this->queryString = ' SELECT '.$fields.' FROM `'.$this->table.'` ';
			return $this;
		}
		
		
		public function orderby($fieldname)
		{
			if(is_string($fieldname) && !is_numeric($fieldname))    {
				$this->queryString .= 'ORDER BY `'.$fieldname.'` ';
			}
			else { $this->queryString = '<br>FAIL!  ORDER BY fieldname "'.$fieldname.'" is not a string <br>';}
			return $this;
		}
		
		public function where($fieldname , $value)
		{
			if(isset($fieldname) && isset($value))  {
				$this->queryString .= 'WHERE `'.$fieldname.'` = "'.$value.'" ';
			}
			else { $this->queryString = '<br>FAIL! WHERE fieldname "'.$fieldname.'" is not a string <br>';}
			
			return $this;
		}
		
		public function andWhere($fieldname , $value)
		{
			if(isset($fieldname) && isset($value))  {
				$this->queryString .= 'AND `'.$fieldname.'` = "'.$value.'" ';
			}
			else { $this->queryString = '<br>FAIL! AND fieldname "'.$fieldname.'" is not a string <br>';}
			
			return $this;
		}
		
		public function orWhere($fieldname , $value)
		{
			if(isset($fieldname) && isset($value))  {
				$this->queryString .= 'OR `'.$fieldname.'` = "'.$value.'" ';
			}
			else { $this->queryString = '<br>FAIL! OR fieldname "'.$fieldname.'" is not a string <br>';}
			
			return $this;
		}
		
		public function groupby($fieldname)
		{
			if(is_string($fieldname) && !is_numeric($fieldname))    {
				$this->queryString = 'GROUP BY `'.$fieldname.'` ';
			}
			else { $this->queryString = '<br>FAIL! GOUP BY fieldname "'.$fieldname.'" is not a string <br>';}
			return $this;
		}
		
		public function whereLike($fieldname, $pattern)
		{
			if(!is_string($fieldname) && is_numeric($fieldname))    {
				$this->queryString = '<br>FAIL! LIKE fieldname "'.$fieldname.'" is not a string <br>';
			}
			elseif(!is_string($pattern) || is_numeric($pattern))    {
				$this->queryString = '<br>FAIL! LIKE fieldname "'.$fieldname.'" is not a string <br>';
			}
			$this->queryString = '`'.$fieldname.'` LIKE \''.$fieldname.'\' ';
			return $this;
		}
		
		/////// insert /// update /// delete /////////////////////
		public function insert($dataArray)
		{
			$fieldnameString = '';
			$valueString = '';
			foreach($dataArray as $key => $value)
			{
				$fieldnameString .= '`'.$key.'`, ';
				$valueString .= '"'.$value.'", ';
			}
			$fieldnames = rtrim( $fieldnameString, ' ,' );
			$values     = rtrim($valueString,' ,' );

			return $this->dbObject->querySQL('INSERT INTO `'.$this->table.'` ('.$fieldnames.') VALUES ('.$values.')');
		}
		
		public function delete($findValue = 0, $whereField = 'id')
		{
			if(! is_numeric($findValue) )  {
				return false;
			}

			return $this->dbObject->querySQL('DELETE FROM `'.$this->table.'` WHERE `'.$whereField.'` = "'.$findValue.'"');
		}
		
		
		public function update($dataArray , $findValue = 0, $whereField = 'id')
		{
			if(is_string($dataArray) )  {
				$dataArray = explode(',',$dataArray);
			}
			
			$fieldsNvalues = '';
			foreach($dataArray as $key => $value)
			{
				$fieldsNvalues .= '`'.$key.'` = "'.$value.'" ,';
			}
			$set =rtrim($fieldsNvalues, ' ,');

			return $this->dbObject->querySQL('UPDATE `'.$this->table.'` SET '.$set.' WHERE `'.$whereField.'` = "'.$findValue.'"');
		}
		
		
		public function raw($sql)
		{
			if(! is_string($sql))  { 			// Warning!!  NO csrf-check
				return false;
			}
			return $this->dbObject->querySQL($sql);
		}
		
	}
