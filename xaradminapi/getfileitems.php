<?php
/**
 * Get records for a file
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Get records for a file
 */
function downloads_adminapi_getfileitems($args)
{

	$linkslist = false;

	extract($args);

	$locations = xarMod::apiFunc('downloads','user','getlocations');

	$key = array_search($location, $locations);

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'downloads',
							'where' => 'location eq \'' . $key . '\' and filename eq \'' . $file
							. '\''));
	$items = $list->getItems();
	$count = count($items);

	$itemids = array();
	$links = array();

	foreach ($items as $item) {
		$itemids[] = $item['itemid'];
		$links[] = '<a href="' . xarModURL('downloads', 'admin', 'modify',
			array('itemid' => $item['itemid'])) . '" class="fileitem"><span>' . $item['itemid'] . '</span></a>';
	}
    
	if (empty($itemids)) {
		return false;
	} elseif (!$linkslist) {
		return $itemids;
	} else {
		if (!empty($links)) {
			return implode(' ',$links);
		} else {
			return false;
		}
	}

}

?>
