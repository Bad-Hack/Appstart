<?php
class Standard_Functions {
	public static $MYSQL_DATETIME_FORMAT = "Y-m-d H:i:s";
	public static $MYSQL_DATE_FORMAT = "Y-m-d";
	public static function getCurrentUser() {
		return Zend_Auth::getInstance ()->getStorage ()->read ();
	}
	public static function getCurrentDateTime($timestamp = null, $format = null) {
		if($format == null){
			$format = Standard_Functions::$MYSQL_DATETIME_FORMAT;
		}
		$datetime = new DateTime($timestamp);
		return $datetime->format ( $format );
	}
}