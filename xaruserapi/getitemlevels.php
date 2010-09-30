<?php
/**
 
/**
 * 
 * Get a one-dimensional array of all items.  Determine the level (generation) of each item.  Because this array recurses there is no attempt at sorting here.
 *
 */
function menutree_userapi_getitemlevels($args)
{

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