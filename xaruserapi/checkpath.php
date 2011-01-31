<?php
/**
 * Check if a path is unique
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
 */
function content_userapi_checkpath($args) {

	$failsilently = false;

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');
	$list = DataObjectMaster::getObjectList(array('name' =>  'content'));
	$filters = array(
						'where' => 'item_path eq \'' . $path . '\''
					);
	$items = $list->getItems($filters);
	if(count($items) == 0) {
		return false;
	} elseif (count($items) == 1) { 
		$item = end($items);
		$itemid = $item['itemid'];
		return $itemid;
	} elseif ($failsilently) {
		return false;
	} else {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item count', 'userapi', 'checkpath', 'content');
        throw new Exception($msg);
		return;
	}
}
?>
