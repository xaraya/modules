<?php
/**
 *  Get a single item
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
function menutree_userapi_getitem($args) {
	$onlyactive = false;

	extract($args);

	if (is_numeric($itemid) && $itemid > 0) {

		$object = DataObjectMaster::getObject(array(
								'name' => 'menutree'
			));
		$object->getItem(array('itemid' => $itemid));
		$item = $object->getFieldValues();

		if($onlyactive) {
			$link = xarMod::apiFunc('menutree','user','link',array('link' => $item['link']));
			if ($link['status'] == 1) {
				return $item;
			} else {
				return '';
			}
		} else {
			return $item;
		}

	} else {
		return false;
	}

}

?>