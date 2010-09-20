<?php
/**
 
***/

function menutree_admin_deleteitems($args) {
	
	extract($args);

	if(!xarVarFetch('deleteIds',       'str',    $itemids,   NULL, XARVAR_DONT_SET)) {return;}

	$itemids = explode(',',$itemids);
 
	sys::import('modules.dynamicdata.class.objects.master');

	foreach ($itemids as $itemid) {
			 
		$object = DataObjectMaster::getObject(array(
							'name' => 'menutree' 
		)); 
		$object->deleteItem(array('itemid' => $itemid)); 

	}
 
	xarResponse::Redirect(xarModURL('menutree','admin','main'));
	return true;

}

?>