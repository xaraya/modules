<?php
/**
 * Administration entry point
 *
 */
function tasks_admin_main()
{
    $data=array();
    $data['welcome']=xarML('Welcome to the administration part of tasks module...');
    $data['pageinfo']=xarML('Overview');
	return $data;
}

?>