<?php
/**
 
***/

function menutree_admin_updateitem($args) {
	
	extract($args);

	if(!xarVarFetch('renameId',       'str',    $itemid,   NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('newName',       'str',    $link,   NULL, XARVAR_DONT_SET)) {return;}
 
	sys::import('modules.dynamicdata.class.objects.master');

	if(isset($itemid) && isset($link))	{	 
		
		$object = DataObjectMaster::getObject(array(
							'name' => 'menutree' 
		));
		$object->getItem(array('itemid' => $itemid));
		$object->properties['link']->setValue($link);
		$object->updateItem();
	}
 
	xarResponse::Redirect(xarModURL('menutree','admin','main'));
	return true;

}

?>