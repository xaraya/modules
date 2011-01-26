<?php

function content_adminapi_ctinfo($args) {

	if(!xarVarFetch('ctype', 'isset', $ctype, NULL, XARVAR_DONT_SET)) {return;}
	
	$content_types = xarMod::apiFunc('content','admin','getcontenttypes');
	
	if(empty($content_types)) return false;

	if(!isset($ctype)) {
		try {
			$ctype = xarModVars::get('content', 'default_ctype');
		} catch (Exception $e) {
		}
	}

	// If the default $ctype has been deleted, don't use the default 
	if(empty($content_types[$ctype])) {
		$ctype = '';
	}

	// No default.  Make the first one (by alpha order) active
	if(empty($ctype)) {
		$ctnames = array_keys($content_types);
		$ctype = reset($ctnames);
	}

	return array('ctype' => $ctype, 'content_types' => $content_types);

}

?>