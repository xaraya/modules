<?php

	$config['PGRFileManager.rootPath'] = 'C:\xampp\htdocs\lsc\html\var\uploads';

	$config['PGRFileManager.urlPath'] = 'http://localhost/lsc/html/var/uploads';

	$config['PGRFileManager.allowedExtensions'] = 'pdf, txt, rtf, jpg, gif, jpeg, png';  //'' means all files

	$config['PGRFileManager.imagesExtensions'] = 'jpg, gif, jpeg, png, bmp';
	
	$config['PGRFileManager.fileMaxSize'] = 1024 * 1024 * 10; // bytes

	$config['PGRFileManager.imageMaxHeight'] = 724;

	$config['PGRFileManager.imageMaxWidth'] = 1280;

	$config['PGRFileManager.allowEdit'] = true;

	// This is from an attempt to allow different paths for different objects...
	/*if (!empty($prop)) {

		//print $prop;
		$pluginsConfiguration[$prop]['PGRFileManager.rootPath'] = 'C:\xampp\htdocs\lsc\html\var\uploads';

		$pluginsConfiguration[$prop]['PGRFileManager.urlPath'] = 'http://localhost/lsc/html/var/uploads';
	
	} else {

		$pluginsConfiguration['default']['PGRFileManager.rootPath'] = 'C:\xampp\htdocs\lsc\html\var\uploads';

		$pluginsConfiguration['default']['PGRFileManager.urlPath'] = 'http://localhost/lsc/html/var/uploads';
	}*/


?>