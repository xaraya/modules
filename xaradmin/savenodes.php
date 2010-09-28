<?php

function menutree_admin_savenodes($args) {

	extract($args);

	if(!xarVarFetch('saveString',       'str',    $savestring,   NULL, XARVAR_DONT_SET)) {return;}

	/* Input to this file - $_GET['saveString']; */

	if(!isset($savestring)) die("no input"); 

	$items = explode(",",$savestring);

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'menutree'));

	foreach ($items as $key=>$item) {

		$vals = explode('-', $item);
		$itemid = $vals[0];
		$parentid = $vals[1];
		$seq = $key;
 
		$object->getItem(array('itemid' => $itemid));
		$object->properties['parentid']->setValue($parentid);
		$object->properties['seq']->setValue($seq);
		$object->updateItem();

	}

	xarResponse::Redirect(xarModURL('menutree','admin','menus'));
	return true;
		 

}


?>