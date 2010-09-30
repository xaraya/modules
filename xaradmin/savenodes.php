<?php
/**
 * Save changes to the hierarchy.  This function is called by savemytree.js
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
function menutree_admin_savenodes($args) {

	extract($args);

	if(!xarVarFetch('saveString',       'str',    $savestring,   NULL, XARVAR_DONT_SET)) {return;}

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