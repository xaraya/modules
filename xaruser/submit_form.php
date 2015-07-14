<?php
sys::import('modules.dynamicdata.class.objects.master');
function pubsub_user_submit_form($args) {
	extract($args);
	
	if (!xarVarFetch('name',        'str',    $name,            'pubsub_subscriptions', XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('userid',      'int',    $userid,       0, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('event_id',    'int',    $event_id,       0, XARVAR_NOT_REQUIRED)) return;
	//if (!xarVarFetch('action_id',   'int',    $action_id,       0, XARVAR_NOT_REQUIRED)) return;
	if (!xarVarFetch('email',       'email', $email,'',XARVAR_NOT_REQUIRED)) return;
	
	// Set some default values
	$default_values = array(
	    'event'   => $event_id,
	    'user_id' => $userid,
	    'email'   => $email,
	    'author'  => xarUser::getVar('uname'),
	);
	
	// Get the object we are working with
	$data['object'] = DataObjectMaster::getObject(array('name' => $name));
	$data['object']->setFieldValues($default_values, 1);

	/*
	// Argument check
	$invalid = array();
	if (!isset($email) || !is_string($email)) $invalid[] = 'email';
	if (!is_numeric($event_id)) $invalid[] = 'event_id';
	if (!is_numeric($userid)) $invalid[] = 'userid';
	//if (!is_numeric($action_id)) $invalid[] = 'action_id';
	*/
	
	if (!empty($invalid)) {
		return xarTpl::module('base','user','test', $data);
	} else {		
		// Good data: create the item
		$itemid = $data['object']->createItem();
		// Jump to the next page
       	xarController::redirect(xarModURL('base','user','test'));
	}
	return $data;
}

?>