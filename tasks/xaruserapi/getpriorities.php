<?php
/**
 * Get task priorities
 *
 */
function tasks_userapi_getpriorities() {
	$priorities = array();
	for($x=0;$x<=9;$x++) {
		$priorities[] = array('id' => $x, 'name' => $x);
	}
    return $priorities;
}

?>