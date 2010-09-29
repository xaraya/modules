<?php
/**
 
/**
 * 
 */
function menutree_userapi_getoptions($args)
{

	$parentid = 0;
	$level = 0;

	extract($args);

	$level = $level + 1;

	if (!isset($arr)) $arr = array();

	/*$prepend = '';
	for ($i=1; $i <= $level; $i++) {
		$prepend .= '-';
	}*/

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'menutree',
							'where' => 'parentid eq ' . $parentid,
							'sort' => 'seq ASC, itemid ASC',
							'numitems' => NULL
		));
	$items = $list->getItems();

	if (!empty($items)) {
		
		foreach($items as $key => $item) { 
 
			$items[$key]['level'] = $level;

			$children = xarMod::apiFunc('menutree','user','getoptions', array('parentid' => $key, 'level' => $level));

			foreach ($children as $key => $item) { 
				$items[$key] = $item;
			}
			
		} 

	} 

	return $items;

}

?>

?>