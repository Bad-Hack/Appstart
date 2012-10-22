<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap {
		public function _initTranslate() {
		$localeValue = 'en';
		
		$locale = new Zend_Locale($localeValue);
		Zend_Registry::set('Zend_Locale', $locale);
		
		$translate = new Zend_Translate(
						    array(
						        'adapter' => 'array',
						        'disableNotices' => true,
						    )
						);
		
		$iterator = new DirectoryIterator(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR);
		foreach ($iterator as $fileinfo) {
			if(!$fileinfo->isDir()) {
				$translate->getAdapter()->addTranslation(array(
							'content' => $fileinfo->getPath(). DIRECTORY_SEPARATOR .$fileinfo->getFilename(),
							'locale' => str_replace(".php", "", $fileinfo->getFilename())
						));
			}
		}
		
		$iterator = new DirectoryIterator(APPLICATION_PATH . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR);
		foreach ($iterator as $fileinfo) {
			if($fileinfo->isDir() && file_exists($fileinfo->getPath(). DIRECTORY_SEPARATOR . $fileinfo->getFilename() . DIRECTORY_SEPARATOR . 'lang')) {
				$langIterator = new DirectoryIterator($fileinfo->getPath() . DIRECTORY_SEPARATOR . $fileinfo->getFilename() . DIRECTORY_SEPARATOR .'lang'. DIRECTORY_SEPARATOR);
				foreach ($langIterator as $langFolder) {
					if(!$langFolder->isDir()) {
						$translate->getAdapter()->addTranslation(array(
								'content' => $langFolder->getPath(). DIRECTORY_SEPARATOR .$langFolder->getFilename(),
								'locale' => str_replace(".php", "", $langFolder->getFilename())
						));
					}	
				}
			}
		}
		$translate->getAdapter()->setLocale($localeValue);
		Zend_Registry::set('Zend_Translate', $translate);
		Zend_Registry::set("app_translate", $translate);
	}
}

