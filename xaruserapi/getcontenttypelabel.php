<?php
/**
 * Utility function for menu items for main menu
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *
 * @return string label
 */
function content_userapi_getcontenttypelabel($args) {

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => $ctype));
	
	return $object->label;
    
}

?>
