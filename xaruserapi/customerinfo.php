<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Get customer info
 */
function shop_userapi_customerinfo($args) {
	
	$values = array();

	if (xarUserIsLoggedIn()) {
		$id = xarUserGetVar('id');
	}

	extract($args);

	if (isset($id)) {
		sys::import('modules.dynamicdata.class.objects.master');
		$custobject = DataObjectMaster::getObject(array('name' => 'shop_customers'));
		$some_id = $custobject->getItem(array('itemid' => $id));
		$values = $custobject->getFieldValues();
		return $values;
	} 

}

?>