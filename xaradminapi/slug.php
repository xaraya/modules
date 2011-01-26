<?php function content_adminapi_slug($args) {  

	$space = '_';
	
	extract($args);

	if (function_exists('iconv')) {  
		setlocale(LC_ALL, 'en_US.UTF8');
		$string = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $string); 
	}  

	$string = trim($string);
	$string = strtolower($string);  
	$string = preg_replace("/[^a-z0-9 _-]/", "", $string); 
	$string = preg_replace('/[\s'.$space.']+/', $space, $string); 
	return $string;  
	
}  
?>