<?php

	if (!empty($prop)) {

		//print $prop;
		$pluginsConfiguration[$prop]['PGRFileManager.rootPath'] = 'C:\xampp\htdocs\lsc\html\var\uploads2';

		$pluginsConfiguration[$prop]['PGRFileManager.urlPath'] = 'http://localhost/lsc/html/var/uploads2';
	
	} else {

		$pluginsConfiguration['default']['PGRFileManager.rootPath'] = 'C:\xampp\htdocs\lsc\html\var\uploads';

		$pluginsConfiguration['default']['PGRFileManager.urlPath'] = 'http://localhost/lsc/html/var/uploads';
	}


?>