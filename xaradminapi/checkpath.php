<?php
/**
 * check if a path is unique
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Path Module
 * @link http://www.xaraya.com/index.php/release/eid/1150
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * @param $args['path'] required string the path
 */
function path_adminapi_checkpath($args) {

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');
	$mylist = DataObjectMaster::getObjectList(array('name' =>  'path'));
	$filters = array(
						'where' => 'path eq \'' . $path . '\''
					);
	$items = $mylist->getItems($filters);
	if(count($items) == 0) {
		return true;
	} else {
		return false;
	}
}
?>
