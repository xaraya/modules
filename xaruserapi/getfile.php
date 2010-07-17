<?php

// Credits: http://www.finalwebsites.com/forums/topic/php-file-download

function downloads_userapi_getfile($args) {

	if (!xarSecurityCheck('ReadDownloads',0)) return;
	
	extract($args);
	 
	if ($fullPath == '/') return false;

	if ($fd = fopen ($fullPath, "rb")) {
	 
		$fsize = filesize($fullPath);
		$path_parts = pathinfo($fullPath);
		$ext = strtolower($path_parts['extension']);

		$mimetype = xarMod::apiFunc('downloads','admin','mtypes',array('ext' => $ext));

		if (!empty($ext)) {
			header("Content-type: " . $mimetype); 
		} else {
			header("Content-type: application/octet-stream");
		}
		header("Content-Disposition: attachment;filename=\"". $path_parts["basename"] ."\""); 
		header("Content-length: $fsize");
		header("Cache-control: private"); //use this to open files directly
		while(!feof($fd)) {
			$buffer = fread($fd, 2048);
			print $buffer;
		}
	}
	fclose ($fd);
	exit; 

}

?>