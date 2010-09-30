<?php
/**
 * Get the portion of the menu tree below the specified parent
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
function menutree_userapi_getmenutree($args) {

	$onlyactive = false;

	extract($args);

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'menutree',
							'where' => 'parentid eq ' . $parentid,
							'sort' => 'seq ASC, itemid ASC',
							'numitems' => NULL
		));
	$items = $list->getItems();

	if (!empty($items)) {
		
		foreach($items as $key => $item) { 

			if($onlyactive) {
				$item = xarMod::apiFunc('menutree','user','getitem',array('itemid' => $key, 'onlyactive' => true));
				if (!$item || empty($item)) { 
					unset($items[$key]); 
				} else {
						$children = xarMod::apiFunc('menutree','user','getmenutree', array('parentid' => $key));
						$items[$key]['children'] = $children;
				}
			} else {
					$children = xarMod::apiFunc('menutree','user','getmenutree', array('parentid' => $key));
					$items[$key]['children'] = $children;	
			}

		} 
	}

	return $items;

}

?>