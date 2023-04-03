<?php
	/**
	 * Project: MVC2022.
	 * Author:  InCubics
	 * Date:    28/06/2022
	 * File:    lib\db\Model.php
	 */
	
	namespace Http\Models;
	
//	use lib\db\mysqliDB;
	use lib\db\mysqliBind as mysqliDB;
	
	class Model
	{
		public $dbObject    = null;
		private $table      = null;
		public $queryString = null;
		public $getList     = false;
		public $inserted_id = null;
		public $affected_rows = null;
		public $num_rows = null;
		public $aggregateKeys = [];
		public $toJson        = false;
		
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
			$this->queryString = 'SELECT * FROM `'.$this->table.'`';
			$this->getList = true;
			return $this;
		}
		
		public function pagination(int $amount=null)
		{
			if(empty($amount)) {
				$this->amount = CONFIG['pagination']['amount'];
			}
			else {
				$this->amount = $amount;
			}
			
			if(empty(request()->get->page)) {
				$this->page = 0;
			}
			else    {
				$this->page = request()->get->page;
			}
			
			$from = (($this->page * $this->amount));
			
			$this->queryString = str_replace('FROM',
				', (SELECT COUNT(`id`) FROM `'.$this->table.'`) AS `total` FROM', $this->queryString);
			$this->queryString .= ' LIMIT '.$from.', '.$this->amount;
			return $this;
		}
		
		public function find($id)
		{
			if(is_numeric( $id))
			{
				$this->queryString = 'SELECT * FROM `'.$this->table.'` WHERE id = ?';
				$this->dbObject->valueArray = array_merge($this->dbObject->valueArray,  ['id'=> $id]);  // collect all params orderd to bind later in get-method: QueryBindParams
			}
			else { $this->queryString = '<br>FAIL!  id "'.$id.'" is not a number <br>';}
			return $this;
		}
		
		public function get($arrayFieldnames = [],  $mode = false)
		{
//dd($this->queryString);
			if(!empty($mode == true))    {
				$this->getList = true;
			}
			// this method is at the end of each method-chain on a Model (not find or all) to send the query to the server
			if(!$this->fillables) { die('Error: No fillables defined in model: '.ucfirst(rtrim($this->table, 's')));}

			if(! $this->dbObject->PrepereParams($this->dbObject->valueArray)){
				die('Failed: prepairing to bind params for MySqli !');
			}

			$dataResult =  $this->dbObject->QueryBindParams($this->queryString, $this->getList);// with BIND_PARAMS

			if($this->dbObject->num_rows > 0 ){
				$this->num_rows = $this->dbObject->num_rows;        // num rows
			}

			if(is_numeric($this->dbObject->inserted_id )){
				$this->inserted_id = $this->dbObject->inserted_id;  // last inserted ID
			}
			if(!empty($this->dbObject->fieldnames )){
				$this->fieldnames = $this->dbObject->fieldnames;    // names of returned fields
			}
			
			if(!empty($this->dbObject->affected_rows )){
				$this->affected_rows = $this->dbObject->affected_rows;  // changed/deleted rows
			}
			if(!empty($this->dbObject->values )){
				$this->values = $this->dbObject->valueArray;            // data that was sent
			}
			$this->sqlString = $this->dbObject->queryString;            // sql-string with placeholders for params
			

			if(isset($this->fillables) ) //// && is_array($arrayFieldnames) //Not with aggregates
			{   // get out only "fillables" keys mentioned in Model, remove other
				/*$dataResponse = [];*/
				
				// select the needed fields
				if(empty($arrayFieldnames) && !empty($this->dbObject->fieldnamesArray)) {  // specific fields requested
					$arrayFieldnames = $this->dbObject->fieldnamesArray;
				}
				if(!empty($this->aggregateKeys)) {  // add aggregate keys and values
					$arrayFieldnames =  array_merge($arrayFieldnames, $this->aggregateKeys);
				}

				if(!empty($this->hidden) && is_array($this->hidden)) { // remove hidden fields from
					$arrayFieldnames = array_diff($arrayFieldnames, $this->hidden);
				}
				
				
				if(($this->dbObject->num_rows > 1 || $this->getList == true) ) { // filter-out hidden fields of multiple records data
					foreach($dataResult as $record) {
						foreach($record as $key => $data)   {
							if(in_array($key, $arrayFieldnames) || $key == 'id') {
								$newRecord[$key]=$data;
							}
						}
						$dataResponse[] = (object) $newRecord;
					}
				}
				elseif($this->dbObject->num_rows == 1)
				{   // filter-out hidden fields in a single set recorddata
					foreach($dataResult as $key => $data)   {
						if(in_array($key, $arrayFieldnames)|| $key == 'id') {
							$newRecord[$key]=$data;
						}
					}
					$dataResponse = (object) $newRecord;
				}
			}
			elseif(isset($this->fillables) && ! is_array($arrayFieldnames))    // no specific fields requested (return all fillables
			{
				$dataResponse = fillableArray((array) $dataResult, (array) $this->fillables);     // return only fillable fields defined in used Model.
			}
			
			if(is_array($dataResponse))
			{
				response_set('query', (object) ['total' => $dataResponse[0]->total,  // for pagination
										'amount' => $this->amount,                  // for pagination
										'num_rows'=> $this->num_rows,
										'affected_rows' => $this->affected_rows,
										'inserted_id' => $this->inserted_id,
										'db_table' => $this->table,
										'object' => $this->dbObject]);
			}
			
			if(!empty($dataResponse) && $this->toJson == true)    {
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($dataResult, JSON_PRETTY_PRINT);
				die;
			}
			elseif(!empty($dataResponse)){
				return $dataResponse;
			}

			return $dataResponse;    // return if Boolean; true || false
		}
		
		//////////
		public function select($arrayFieldNames = '*', $distinct = false)
		{
			if($distinct == false){
				$selectPart = 'SELECT';
			}
			else {
				$selectPart = 'SELECT DISTINCT';
			}
			
			if($arrayFieldNames != '*' && is_array($arrayFieldNames))   {
				$fields ='';
				foreach($arrayFieldNames as $key => $fieldName)
				{
					$agg = strtolower(substr($key,0, 3));
					$fieldNameArgg = $agg.ucfirst($fieldName);
					if(in_array($agg, ['avg', 'sum', 'min', 'max']))   {   // Aggregate possibilities
						$fields.= strtoupper($agg).'(`'.$fieldName.'`) AS `'.$agg.ucfirst($fieldName).'`, ';
						$this->fillables[$fieldNameArgg] = $fieldNameArgg;  // add aggregateRequest to Fillables
						$this->aggregateKeys[] = $fieldNameArgg;
					}
					else    {       // adding a single record-field to selection
						$fields.='`'.$fieldName.'`, ';
					}
				}
				$fields = rtrim($fields, ', ');
			}
			else    {
				$fields = '* ';
			}
			$this->queryString = $selectPart.' '.$fields.' FROM `'.$this->table.'` ';
			return $this;
		}
		
		public function orderby($fieldname)     //  array for multiple ordering || string eith single ordering
		{
			$this->queryString .= 'ORDER BY ';
			if(is_array($fieldname))
			{
				$string = '';
				foreach($fieldname as $field){
					$string .= '`'.$field.'`, ';
				}
				$this->queryString .= rtrim( $string, ', ');
			}
			elseif(is_string($fieldname) && !is_numeric($fieldname))    {
				$this->queryString .= '`'.$fieldname.'` ';
			}
			else { $this->queryString = '<br>FAIL!  ORDER BY fieldname "'.$fieldname.'" is not a string <br>';}
			
			return $this;
		}
		
		public function limit($from, $count)
		{
			if(is_numeric($from) && $from > 0 && is_numeric($count) && $count > 0 )    {
				$this->queryString .= 'LIMIT '.$from.', '.$count. ' ';
			}
			else { $this->queryString = '<br>FAIL!  LIMIT requires numbers is not a string <br>';}
			return $this;
		}
		
		public function where($fieldname, $value = null, $operator = '=')
		{
			if($operator != '=' &&  ! in_array($operator, ['=', '<>', '!=', '<=', '>=', '<', '>', 'IS NOT' ])) {
				$this->queryString = '<br>FAIL! INVALID comparison operator, third param in method Where<br>';
			}
			
			if(isset($fieldname) && isset($value))  {
				$this->queryString .= 'WHERE `'.$fieldname.'` '.$operator.' ? ';
				$this->dbObject->valueArray = array_merge($this->dbObject->valueArray,  [$fieldname=> $value]);  // collect all params orderd to bind later in get-method: QueryBindParams
			}
			else { $this->queryString = '<br>FAIL! WHERE fieldname "'.$fieldname.'" is not a string <br>';}

			return $this;
		}
		
		public function andWhere($fieldname , $value, $operator = '=')
		{
			if($operator != '=' &&  ! in_array($operator, ['=', '<>', '!=', '<=', '>=', '<', '>', 'IS NOT' ])) {
				$this->queryString = '<br>FAIL! INVALID comparison operator, third param in method andWhere<br>';
				}
			
			if(isset($fieldname) && isset($value))  {
				$this->queryString .= 'AND `'.$fieldname.'`'.$operator.' ? ';
				$this->dbObject->valueArray = array_merge($this->dbObject->valueArray,  [$fieldname=> $value]);  // collect all params orderd to bind later in get-method: QueryBindParams
			}
			else { $this->queryString = '<br>FAIL! AND fieldname "'.$fieldname.'" is not a string <br>';}
			
			return $this;
		}
		
		public function orWhere($fieldname , $value,  $operator = '=')
		{
			if($operator != '=' &&  ! in_array($operator, ['=', '<>', '!=', '<=', '>=', '<', '>', 'IS NOT' ])) {
				$this->queryString = '<br>FAIL! INVALID comparison operator, third param in method orWhere<br>';
			}
			if(isset($fieldname) && isset($value))  {
				$this->queryString .= 'OR `'.$fieldname.'` '.$operator.' ? ';
				$this->dbObject->valueArray = array_merge($this->dbObject->valueArray,  [$fieldname=> $value]);  // collect all params orderd to bind later in get-method: QueryBindParams

			}
			else { $this->queryString = '<br>FAIL! OR fieldname "'.$fieldname.'" is not a string <br>';}
			
			return $this;
		}
		
		public function groupby($fieldname)
		{   //TODO argument usage as an string & array
			if(is_string($fieldname) && !is_numeric($fieldname))    {
				$this->queryString .= 'GROUP BY `'.$fieldname.'` ';
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
			$this->queryString = '`'.$fieldname.'` LIKE \''.$pattern.'\' ';
			return $this;
		}
		
		/////// insert /// update /// delete /////////////////////
		public function insert($dataArray)
		{
			if(! $this->dbObject->PrepereParams($dataArray)){
				die('Failed: prepairing to bind params for MySqli !');
			}
			$stmt = 'INSERT INTO '.$this->table.' ('.$this->dbObject->fieldnames.') VALUES ( '.$this->dbObject->qMarkString.' )';
			$this->sqlString = $stmt;
			if($this->dbObject->QueryBindParams($stmt))
			{
				$this->inserted_id = $this->dbObject->inserted_id;
				return true;
			}
			return false;
		}
		
		public function delete($findValue = 0, $whereField = 'id', $operator = '=')
		{
			if($operator != '=' &&  ! in_array($operator, ['=', '<>', '!=', '<=', '>=', '<', '>', 'IS', 'IS NOT' ])) {
				$this->queryString = '<br>FAIL! INVALID comparison operator, third param in method Where<br>';
			}
//			$this->dbObject->valueArray = array_merge($this->dbObject->valueArray,  [$whereField=> $findValue]);
			if(! $this->dbObject->PrepereParams([$whereField => $findValue])){
				die('Failed: prepairing to bind params for MySqli !');
			}
			$stmt = 'DELETE FROM `'.$this->table.'` WHERE `'.$whereField.'` '.$operator.' ? ';
			$this->sqlString = $this->dbObject->stmt;
			if($this->dbObject->QueryBindParams($stmt))
			{
				$this->affected_rows = $this->dbObject->affected_rows;
				return true;
			}
			return false;
		}
		
		public function update($dataArray , $findValue = 0, $whereField = 'id', $operator = '=')
		{
			if($operator != '=' &&  ! in_array($operator, ['=', '<>', '!=', '<=', '>=', '<', '>', 'IS', 'IS NOT' ])) {
				$this->queryString = '<br>FAIL! INVALID comparison operator, third param in method Where<br>';
			}

			$params = array_merge((array)$dataArray, [$whereField => $findValue]);

			if(! $this->dbObject->PrepereParams($params))   {
				die('Failed: prepairing to bind params for MySqli !');
			}

			$set = '';
			foreach($dataArray as $key => $value) {
				if( empty($key)  &&  empty($value)) {
					$this->queryString = '<br>FAIL! given key: '.$key.' has no value<br>';
				}
				$set .= '`'.$key.'` = ?, ';
			}
			$set =rtrim($set, ' ,');

			$stmt = 'UPDATE `'.$this->table.'` SET '.$set.' WHERE `'.$whereField.'` '.$operator.' ? ';
			$this->sqlString = $stmt;

			$result = $this->dbObject->QueryBindParams($stmt);
			
			$this->error_list    = $this->dbObject->error_list;
			$this->affected_rows = $this->dbObject->affected_rows;

			if($result == true) {
				return true;
			}
			
			return false;
		}
		
		/////// RAW - queries with bind params /// ///  /////////////////////
		public function raw($stmt, $dataArray)
		{
			if(! $this->dbObject->PrepereParams($dataArray)){
				die('Failed: prepairing to bind params for MySqli !');
			}
			
			$this->sqlString = $stmt;
			if($result = $this->dbObject->QueryBindParams($stmt))
			{
				$result = (object) array_merge($result,
					['meta' => (object) [
						'numrows'=>$this->dbObject->num_rows,
						'inserted_id' =>$this->dbObject->inserted_id,
						'affected_rows' =>$this->dbObject->affected_rows,
						'fieldnames' =>$this->dbObject->fieldnames,
						'values' =>$this->dbObject->values,
						'postedValues' =>$this->dbObject->valueArray,
						'queryString' =>$this->dbObject->queryString
					]]
				);
				return $result;
			}
			return false;
		}
		
		
		/////// Relations - JOIN ////  1-to-many /// many-to-many ///  /////////////////////
		public function join(string $foreinKeyThisTable , string $primKeyOtherTable, string $otherTable, $typeClause = 'INNER')
		{
			if(strtoupper($typeClause)!='INNER')
			{
				if(!in_array($typeClause, ['INNER', 'OUTER', 'LEFT', 'RIGHT', 'CROSS']))
				{
					$this->queryString='<br>FAIL! to '.$typeClause.' JOIN table: `'.$this->table.'` with `'.$primKeyOtherTable.'` <br>';
				}
			}
			$this->queryString .= $typeClause.' JOIN `'.$otherTable.'` ON `'.$this->table.'`.`'.$foreinKeyThisTable.'`  ON `'.$otherTable.'`.'.$primKeyOtherTable.'` ';
			
			return $this;
		}
		
		public function oneOnMany(string $foreinKeyThisTable , string $primKeyOtherTable, string $otherTable)
		{   // this method is chainable for several joins in the query-string
			$this->queryString .= 'INNER JOIN `'.$otherTable.'` ON `'.$this->table.'`.`'.$foreinKeyThisTable.'` = `'.$otherTable.'`.`'.$primKeyOtherTable.'` ';

			return $this;
		}
		
		public function manyOnMany( string $othetModelName)
		{
			$ns = 'Http\\Models\\'.$othetModelName;
			if (!class_exists($ns)) {
				die('Failed: Relation with Model-class not possible, class: '.$ns.' not found!');
			}
			
			$otherModel = (new $ns());
			if(!empty($otherModel->table)){
				$otherTableName = $otherModel->table;
			}
			else {
				$otherTableName  = rtrim(strtolower($othetModelName), 's');
			}
			
			if(!empty($otherModel->primary)){
				$primKeyOtherTable = $otherModel->primary;
			}
			else {
				$primKeyOtherTable = 'id';
			}
			
			/// this-Model
			if(!empty($this->table)){
				$thisTable = $this->table;
			}
			else {
				$thisTable = rtrim(strtolower($othetModelName), 's');
			}
			
			if(!empty($this->primary)){
				$thisTableId = $this->primary;
			}
			else {
				$thisTableId = 'id';
			}

			/// Pivot-table ////
			///  eq: model User with primary = 'id' & other model Fruit with primary = 'nr'
			///  pivot tablename must be :  user_fruit
			///  with fields :  `id `  and   `nr` ==> together they are primary
			$pivot = $thisTable.'_'.$otherTableName;
			$pivotThisId  = rtrim($thisTable, 's').'_'.$thisTableId;
			$pivotOtherId = rtrim($otherTableName, 's').'_'.$primKeyOtherTable;
			
			$this->queryString .= ' inner   JOIN `'.$pivot.'`
								ON `'.$this->table.'`.`'.$thisTableId.'` = `'.$pivot.'`.`'.$pivotThisId.'` ';
			$this->queryString .= 'inner JOIN `'.$otherTableName.'`
								ON `'.$pivot.'`.`'.$pivotOtherId.'` = `'.$otherTableName.'`.`'.$primKeyOtherTable.'` ';
			
			return $this;
			
			
/*			SELECT `users`.* ,
CAST(GROUP_CONCAT(`users_fruits`.`user_id`, DATA_SUBDELIMITER,
	`users_fruits`.`likes`, DATA_SUBDELIMITER, `users_fruits`.`comment`
) AS CHAR) AS related
 FROM `users`
 JOIN `users_fruits` ON `users`.`id` = `users_fruits`.`user_id`*/
		
		}

		
		/////// json /// /////////////////////
		public function toJson()
		{   // change flag to return a data in json-format
			$this->toJson = true;
			return $this;
		}
	}
