<?php
/**
 
***/

function menutree_admin_deleteitem($args) {
	
	extract($args);

	if(!xarVarFetch('deleteIds',       'str',    $itemid,   NULL, XARVAR_DONT_SET)) {return;}
 
	sys::import('modules.dynamicdata.class.objects.master');

	if(isset($itemid))	{	 
		
		$object = DataObjectMaster::getObject(array(
							'name' => 'menutree' 
		));
		$object->getItem(array('itemid' => $itemid));
		$object->deleteItem();
	}
 
	xarResponse::Redirect(xarModURL('menutree','admin','main'));
	return true;

}

?>