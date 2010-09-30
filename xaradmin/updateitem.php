<?php
/**
 * Update an item's link field.  This function is called by drag-drop-folder-tree.js
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