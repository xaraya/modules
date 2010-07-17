<?php

// Credits: http://www.finalwebsites.com/forums/topic/php-file-download

function downloads_userapi_getlocations($args) {

	extract($args);

	$dirs = xarModVars::get('downloads', 'file_directories');

	include(sys::varpath(). '/config.system.php');

	if (!empty($systemConfiguration['downloads.basepath'])) {
		$basepath = $systemConfiguration['downloads.basepath'];
	} else {
		$basepath = '../';
	}

	$dirs = str_replace(' ',"\r",$dirs);
	$dirs = str_replace(',',"\r",$dirs);
	$dirs = str_replace('\n',"\r",$dirs);

	// For security
	$dirs = str_replace('/','',$dirs);
	$dirs = str_replace('.','',$dirs);
	$dirs = str_replace('\\','',$dirs);
	
	$dirs = explode("\r",$dirs);
	
	foreach ($dirs as $key=>$value) {
		
		$value = $basepath . trim($value);
		if (strlen($value) > 0) {
			$locations[$value] = $value;
		}
	} 

	return $locations;

}

?>