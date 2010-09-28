<?php
/**
 
/**
 * 
 */
function menutree_userapi_getmenutree($args)
{
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