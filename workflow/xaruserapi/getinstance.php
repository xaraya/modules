<?php

/**
	Addition to the workflow module when there is a need
	to retrieve the actual instance rather than just an
	array of values. This can be used in conjunction with
	the "findinstances" api.

	@author Mike Dunn submitted by Court Shrock
	@access public
	@param $instaceId (required)
	@returns workflow Instance
*/
function workflow_userapi_getInstance($args) {

	include_once('modules/workflow/tiki-setup.php');

	//make sure this user an access this instance
	if (!xarSecurityCheck('ReadWorkflow')) return;

	extract($args);

	//if not instance is set send this back we cannon continue
	if(!isset($instanceId)) return;

	//check to see if this hasn't alredy been done
	if(!function_exists("getInstance")){
		include_once(GALAXIA_LIBRARY.'/API.php');
	}

	$inst = new Instance($dbGalaxia);
	$inst->getInstance($instanceId);

	return $inst;
}

?>
