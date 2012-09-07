<?php
class Standard_Functions {
	public static $MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
	public static $MYSQL_DATE_FORMAT = "Y-m-d";
	public static function getCurrentUser() {
		return Zend_Auth::getInstance ()->getStorage ()->read ();
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