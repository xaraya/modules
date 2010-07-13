<?php

// Credits: http://www.finalwebsites.com/forums/topic/php-file-download

function downloads_userapi_getlocations($args) {

	extract($args);

	$dirs = xarModVars::get('downloads', 'file_directories');

	$dirs = str_replace(' ',"\r",$dirs);
	$dirs = str_replace(',',"\r",$dirs);
	$dirs = str_replace('\n',"\r",$dirs);
	
	$dirs = explode("\r",$dirs);
	
	foreach ($dirs as $key=>$value) {
		$value = trim($value);
		if (strlen($value) > 0) {
			$locations[$value] = $value;
		}
	}
 
	return $locations;

}

?>