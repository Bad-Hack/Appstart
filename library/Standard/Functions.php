<?php
class Standard_Functions {
	public static $MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
	public static $MYSQL_DATE_FORMAT = "Y-m-d";
	public static function getCurrentUser() {
		return Zend_Auth::getInstance ()->getStorage ()->read ();
	}
	public static function getActiveLanguage() {
		$language_id = Zend_Auth::getInstance ()->getStorage ()->read ()->active_language_id;
		$mapper = new Admin_Model_Mapper_Language();
		$lang = $mapper->find($language_id);
		return $lang;
	}
	public static function getAdminActiveLanguage() {
		$language_id = Zend_Auth::getInstance ()->getStorage ()->read ()->active_language_id_admin;
		$mapper = new Admin_Model_Mapper_Language();
		$lang = $mapper->find($language_id);
		return $lang;
	}
	public static function getAllLanguages() {
		$mapper = new Admin_Model_Mapper_Language();
		$lang = $mapper->getDbTable ()->fetchAll()->toArray();
		return $lang;
	}
	public static function getCustomerLanguages() {
		$model=false;
		if(isset(self::getCurrentUser()->customer_id)) {
			$mapper = new Admin_Model_Mapper_Language();
			$select = $mapper->getDbTable ()->
							select ( false )->
							setIntegrityCheck ( false )->
							from ( array ("l" => "language"), array (
									"l.language_id" => "language_id",
									"l.title" => "title",
									"lang","logo") )->
							joinLeft ( array ("cl" => "customer_language"), "l.language_id = cl.language_id",
									array ("cl.customer_id") )->
							where("cl.customer_id=".self::getCurrentUser()->customer_id);
							
			$model = $mapper->getDbTable ()->fetchAll($select)->toArray();
			foreach($model as $key=>$val) {
				$model[$key]["language_id"] = $val["l.language_id"];
				$model[$key]["title"] = $val["l.title"];
			}
		}
		return $model;
	}
	public static function getCurrentDateTime($timestamp = null, $format = null) {
		if ($format == null)
			$format = Standard_Functions::$MYSQL_DATETIME_FORMAT;
		if ($timestamp == null)
			$timestamp = time ();
		$datetime = new DateTime ();
		
		$datetime->setTimestamp ( $timestamp );
		return $datetime->format ( $format );
	}
	public static function getDefaultDbAdapter() {
		return Zend_Db_Table::getDefaultAdapter ();
	}
	public static function getResourcePath() {
		return APPLICATION_PATH . "/../public/resource/";
	}
}