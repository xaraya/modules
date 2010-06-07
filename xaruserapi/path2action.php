<?php
/**
 * Get an action for a path
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
 *  get an action for a path
 * @param args[$path] required string path
 */
function path_userapi_path2action($args)
{
	extract($args);

    sys::import('modules.dynamicdata.class.objects.master');
    sys::import('modules.dynamicdata.class.properties.master');

	$mylist = DataObjectMaster::getObjectList(array('name' =>  'path'));

	$filters = array(
					 'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
					 'where'      => 'path eq \'' . $path . '\'',
					);
	
	$items = $mylist->getItems($filters);

	$qs = array();

	if(count($items) == 1) {
		$item = end($items);
		$action = $item['action'];
		$action = unserialize($action);
		return $action;
	} else {
		return false;
	}
}

?>
