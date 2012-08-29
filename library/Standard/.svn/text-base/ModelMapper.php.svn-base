<?php
abstract class Standard_ModelMapper implements Standard_MapperStandards {
	protected $_dbTableClass = "";
	protected $_modelClass = "";
	protected $_dbTable;
	final public function __construct() {
		// Set DbTable Class
		if ($this->_dbTableClass == "" || $this->_dbTableClass === null)
			$this->_dbTableClass = str_ireplace ( "_Mapper_", "_DbTable_", get_class ( $this ) );
			
			// Set Model Class
		if ($this->_modelClass == "" || $this->_modelClass === null)
			$this->_modelClass = str_ireplace ( "_Mapper", "", get_class ( $this ) );
	}
	final public function setDbTable($dbTable) {
		if (is_string ( $dbTable )) {
			$dbTable = new $dbTable ();
		}
		if (! $dbTable instanceof Zend_Db_Table_Abstract) {
			throw new Exception ( 'Invalid table data gateway provided for ContentsetMapper classs' );
		}
		$this->_dbTable = $dbTable;
		return $this;
	}
	public function getDbTable() {
		if ($this->_dbTable == null || $this->_dbTable === null) {
			$this->setDbTable ( $this->_dbTableClass );
		}
		return $this->_dbTable;
	}
	final public function __call($method, $arguments) {
		$db = $this->getDbTable ();
		$methods = get_class_methods ( get_class ( $db ) );
		if (in_array ( $method, $methods )) {
			return call_user_func_array ( array (
					$db,
					$method 
			), $arguments );
		} else {
			throw new Zend_Exception ( 'Invalid Method: ' . $method . '()' );
		}
	}
	
	/**
	 * Overriding the default find functionality to return models
	 *
	 * @return Ambigous <boolean, unknown, multitype:unknown >
	 */
	public function find() {
		
		// Return false if not output is found
		$models = false;
		
		// Call the original Find function of the Zend_DbTable
		$args = func_get_args ();
		$originalFindOutput = call_user_func_array ( array (
				$this->getDbTable (),
				__FUNCTION__ 
		), $args );
		
		$originalFindOutputArray = $originalFindOutput->toArray ();
		
		if (is_array ( current ( $originalFindOutputArray ) ) && isset ( $originalFindOutputArray [0] ) && ! empty ( $originalFindOutputArray [0] )) {
			$models = array ();
			// For more than one result for single primary key
			foreach ( $originalFindOutputArray as $findOutput ) {
				$model = new $this->_modelClass ( $findOutput );
				$models [] = $model;
			}
			if (count ( $originalFindOutputArray ) == 1) {
				$models = $models [0];
			}
		}
		return $models;
	}
	
	/**
	 * Fetches all Models.
	 *
	 * Honors the Zend_Db_Adapter fetch mode.
	 *
	 * @param string|array|Zend_Db_Table_Select $where
	 *        	OPTIONAL An SQL WHERE clause or Zend_Db_Table_Select object.
	 * @param string|array $order
	 *        	OPTIONAL An SQL ORDER clause.
	 * @param int $count
	 *        	OPTIONAL An SQL LIMIT count.
	 * @param int $offset
	 *        	OPTIONAL An SQL LIMIT offset.
	 * @return Ambigous <boolean, unknown, multitype:unknown >
	 */
	public function fetchAll($where = null, $order = null, $count = null, $offset = null) {
		$models = false;
		
		$originalFetchAllOutput = $this->getDbTable ()->fetchAll ( $where, $order, $count, $offset );
		
		$originalFetchAllOutputArray = $originalFetchAllOutput->toArray ();
		
		if (is_array ( current ( $originalFetchAllOutputArray ) ) && isset ( $originalFetchAllOutputArray [0] ) && ! empty ( $originalFetchAllOutputArray [0] )) {
			
			$models = array ();
			
			// For more than one result for single primary key
			foreach ( $originalFetchAllOutputArray as $findOutput ) {
				$model = new $this->_modelClass ( $findOutput );
				$models [] = $model;
			}
		}
		return $models;
	}
	
	public function save(Standard_Model $model) {
		// public function save(array $model) {
		if (! ($model instanceof $this->_modelClass)) {
			$classProvided = get_class ( $model );
			throw new Zend_Exception ( "Wrong modelClass [{$classProvided}] given to mapper of model [{$this->_modelClass}]" );
		}
		
		// Get PrimaryKey by conventions
		$primaryKey = $this->_getPrimaryKeyName ();
		
		$modelData = $model->toArray ();
		if ($modelData [$primaryKey] != null || $modelData [$primaryKey] !== null) {
			// Update the existing Record
			$this->getDbTable ()->update ( $model->getUpdatedVars (), " " . $primaryKey . " = " . $modelData [$primaryKey] );
			return $this->find ( $modelData [$primaryKey] );
		} else {
			// Insert the new record
			$insert_id = $this->getDbTable ()->insert ( $modelData );
			$model->set ( $primaryKey, $insert_id );
		}
		return $model;
	}
	
	/**
	 * Deletes existing rows.
	 *
	 * @param  array|string $where SQL WHERE clause(s).
	 * @return int          The number of rows deleted.
	 */
	public function delete($where){
		return $this->getDbTable()->delete($where);
	}
	public function countAll($filter) {
		return $this->getDbTable ()->fetchAll ( $filter )->count ();
	}
	
	/**
	 * Create variable according to the conventions
	 *
	 * @param string $method        	
	 * @return string
	 */
	private function _createVariable($method) {
		$string = "";
		for($i = 0; $i < strlen ( $method ); $i ++) {
			if ($method [$i] == strtoupper ( $method [$i] )) {
				$string .= "_" . strtolower ( $method [$i] );
			} else {
				$string .= $method [$i];
			}
		}
		return $string;
	}
	
	/**
	 * Get the computed primary key name according to convetion
	 *
	 * @return string
	 */
	private function _getPrimaryKeyName() {
		$nameArray = explode ( "_", $this->_modelClass );
		$name = array_pop ( $nameArray );
		$name = $this->_createVariable ( $name );
		$name = substr ( $name, 1 );
		$primaryKey = $name . "_id";
		
		unset ( $nameArray );
		unset ( $name );
		
		return $primaryKey;
	}
}