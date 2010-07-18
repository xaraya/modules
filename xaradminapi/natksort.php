<?php

function downloads_adminapi_natksort($args) {
	
	extract($args);

	$result = array();
	$arrkeys = array_keys($arr2sort);
	natcasesort($arrkeys);

	foreach ($arrkeys as $key) {
		$result[$key] = $arr2sort[$key];
	}

	return $result;
}

?>