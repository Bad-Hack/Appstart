<?php
class Admin_Model_Mapper_Template extends Standard_ModelMapper {
	protected $_dbTableClass = "Admin_Model_DbTable_Template";
	
	/**
	 * Get Grid Data
	 *
	 * @param array $columns        	
	 * @param string $where        	
	 * @return boolean multitype:multitype:string
	 */
	public function getGridData(array $options = array(), $where = null) {
		
		// Get the current request object
		$request = Zend_Controller_Front::getInstance ()->getRequest ();
		// Calculate Columns required
		$columns = $request->getParam ( 'sColumns' );
		$columns = explode ( ",", $columns );
		$columns = array_filter ( $columns, function ($value) {
			return ($value != "");
		} );
		
		// Applying Sorting
		$order = "";
		$iSortingCols = $request->getParam ( 'iSortingCols' );
		for($i = 0; $i < intval ( $iSortingCols ); $i ++) {
			if ($request->getParam ( "bSortable_" . $request->getParam ( 'iSortCol_' . $i ), false )) {
				$order .= $columns [$request->getParam ( 'iSortCol_' . $i )] . " " . $request->getParam ( 'sSortDir_' . $i ) . ", ";
			}
		}
		// Change sOrder back to null
		$order = $order == "" ? null : $order;
		
		// Extract Searching Fields
		$allParams = $request->getParams ();
		$searchParams = array_filter ( $allParams, function ($key) use(&$allParams) {
			if (strpos ( key ( $allParams ), "search_" ) !== false && $allParams [key ( $allParams )] != "") {
				next ( $allParams );
				return true;
			} else {
				next ( $allParams );
				return false;
			}
		} );
		
		// Check for replace columns bbefore setting data to data grid
		$replaceColumns = false;
		if (isset ( $options ["column"] ) && isset ( $options ["column"] ["replace"] )) {
			$replaceColumns = array_keys ( $options ["column"] ["replace"] );
		}
		// var_dump($replaceColumns);
		// Searching
		if (! empty ( $searchParams )) {
			if ($where == "") {
				$where .= " (";
			} else {
				$where .= " AND ";
			}
			// Before Search Params
			foreach ( $searchParams as $searchColumn => $searchValue ) {
				if (is_array ( $searchValue )) {
					foreach ( $searchValue as $key => $value ) {
						$searchParams [$searchColumn . "." . $key] = $value;
					}
					unset ( $searchParams [$searchColumn] );
				}
			}
			
			foreach ( $searchParams as $searchColumn => $searchValue ) {
				
				$searchColumn = substr ( $searchColumn, strlen ( "search_" ) );
				
				// Creating custom search for replacement properties
				if ($replaceColumns && in_array ( $searchColumn, $replaceColumns )) {
					$filterReplaceColumns = $options ['column'] ['replace'] [$searchColumn];
					$searchArray = array_filter ( $filterReplaceColumns, function ($data) use(&$filterReplaceColumns, $searchValue) {
						if (strpos ( strtolower ( current ( $filterReplaceColumns ) ), strtolower ( $searchValue ) ) !== false) {
							next ( $filterReplaceColumns );
							return true;
						}
						next ( $filterReplaceColumns );
						return false;
					} );
					if (! empty ( $searchArray )) {
						$where .= "( ( ";
						foreach ( $searchArray as $key => $value ) {
							$where .= $searchColumn . " LIKE '%" . $key . "%' OR ";
						}
						$where = substr_replace ( $where, "", - 3 );
						$where .= " ) OR " . $searchColumn . " LIKE '%" . $searchValue . "%' ) AND ";
					} else {
						$where .= $searchColumn . " LIKE '%" . $searchValue . "%' AND ";
					}
				} else {
					$where .= $searchColumn . " LIKE '%" . $searchValue . "%' AND ";
				}
			}
			
			$where = substr_replace ( $where, "", - 4 );
			$where .= ") ";
		}
		// print_r($searchParams);
		// die;
		$where = $where == "" ? "1=1" : $where;
		// Get the data from database
		// Set Offset and Limit/Count
		$count = $request->getParam ( "iDisplayLength", 10 );
		$offset = $request->getParam ( "iDisplayStart", 0 );
		
		// $models = $this->fetchAll ( $where, $order , $count , $offset);
		
		$modelsSql = $this->getDbTable ()->select ( false )->setIntegrityCheck ( false )->from ( array (
				"t" => "template" 
		), array (
				"t.template_id",
				"t_name" => "t.name",
				"t_status" => "t.status",
				"t_last_updated_at" => "t.last_updated_at" 
		) )->joinLeft ( array (
				"bt" => "business_type" 
		), "bt.business_type_id=t.business_type_id", array (
				"business_type" => "bt.name" 
		) )->joinLeft ( array (
				"tm" => "template_module" 
		), "tm.template_id=t.template_id", array (
				"total_modules" => "count(tm.template_module_id)" 
		) )->group ( "t.template_id" )->where ( $where )->order ( $order )->limit ( $count, $offset );
		$models = $this->getDbTable ()->fetchAll ( $modelsSql )->toArray ();
		$gridData = array ();
		if ($models) {
			foreach ( $models as $model ) {
				$record = array ();
				foreach ( $columns as $column ) {
					if (isset ( $options ["column"] ) && isset ( $options ["column"] ["id"] ) && in_array ( $column, $options ["column"] ["id"] )) {
						$record [] = $model;
					} else if (isset ( $options ["column"] ) && isset ( $options ["column"] ["ignore"] ) && in_array ( $column, $options ["column"] ["ignore"] )) {
						$record [] = "";
					} else {
						$columnValue = $model [$column];
						
						if ($replaceColumns && in_array ( $column, $replaceColumns ) && isset ( $options ["column"] ["replace"] [$column] [$columnValue] )) {
							$record [] = $options ["column"] ["replace"] [$column] [$columnValue];
						} else {
							$record [] = $columnValue;
						}
					}
				}
				$gridData [] = $record;
			}
		}
		$total = count ( $this->getDbTable ()->select ( false )->from ( array (
				"t" => "template" 
		), array (
				"t.*" 
		) )->joinLeft ( array (
				"bt" => "business_type" 
		), "bt.business_type_id=t.business_type_id", array (
				"business_type" => "bt.name" 
		) ) );
		$totalFiltered = count ( $this->getDbTable ()->select ( false )->from ( array (
				"t" => "template" 
		), array (
				"t.*" 
		) )->joinLeft ( array (
				"bt" => "business_type" 
		), "bt.business_type_id=t.business_type_id", array (
				"business_type" => "bt.name" 
		) )->where ( $where ) );
		
		$finalGridData ["sEcho"] = $request->getParam ( "sEcho", 1 );
		
		$finalGridData ["iTotalRecords"] = $total;
		$finalGridData ["iTotalDisplayRecords"] = $totalFiltered;
		$finalGridData ["aaData"] = $gridData;
		
		return $finalGridData;
	}
}