<?php
/**
 * Get status options
 *
 */
function tasks_userapi_getstatusoptions() 
{
	$statusoptions = array();    
	$statusoptions[] = array('id'=>0,'name'=>xarML('Open'));
	$statusoptions[] = array('id'=>1,'name'=>xarML('Closed'));
    return $statusoptions;
}

?>