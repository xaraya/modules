<?php
/**
 * Get a one-dimensional array of all the items along with their levels (generation)
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
function menutree_userapi_getitemlevels($args) {

	$parentid = 0;
	$level = 0;

	extract($args);

	$level = $level + 1;

	if (!isset($arr)) $arr = array();

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'menutree',
							'where' => 'parentid eq ' . $parentid, 
							'numitems' => NULL
		));
	$items = $list->getItems();

	if (!empty($items)) {
		
		foreach($items as $key => $item) { 
 
			$items[$key]['level'] = $level;

			$children = xarMod::apiFunc('menutree','user','getitemlevels', array('parentid' => $key, 'level' => $level));

			foreach ($children as $key => $item) { 
				$items[$key] = $item;
			}
			
		} 

	} 

	return $items;

}

?>

?>