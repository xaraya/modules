<?php
/**
 * Delete multiple items.  This function is called by drag-drop-folder-tree.js
 *
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/eid/1162
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  
 */
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