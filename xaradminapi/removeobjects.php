<?php
/**
 * Utility function to remove the native objects of this module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Tasks module
 */
/**
 * utility function to remove the native objects of this module
 *
 * @author Marc Lutolf <marcinmilan@xaraya.com>
 * @returns boolean
 */
function tasks_adminapi_removeobjects($args)
{
	extract($args);
	if (empty($itemtypes)) $itemtypes = array(1,2,3);
	$moduleid = 667;
	foreach ($itemtypes as $itemtype) {
		$info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('moduleid' => $moduleid, 'itemtype' => $itemtype));
		$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $info['objectid']));
	}
	$info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('moduleid' => 667, 'itemtype' => 1));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $info['objectid']));
	$info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('moduleid' => 667, 'itemtype' => 2));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $info['objectid']));
	$info = xarModAPIFunc('dynamicdata','user','getobjectinfo',array('moduleid' => 667, 'itemtype' => 3));
	$result = xarModAPIFunc('dynamicdata','admin','deleteobject',array('objectid' => $info['objectid']));
    return true;
}

?>
