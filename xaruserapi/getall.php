<?php
 
function menutree_userapi_getall($args) {

	$delimiter = '|';
	$forvalidation = false;
	$filters = array();
	
	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');

	$list = DataObjectMaster::getObjectList(array(
							'name' => 'menutree'
		));
	$items = $list->getItems($filters);

	if ($forvalidation) {
		foreach($items as $key => $item) {
			if(strstr($item['link'], $delimiter)) {
				$pos = strpos($item['link'], $delimiter);
				$item['link'] = substr($item['link'], 0, $pos);
			}
			$link = trim($item['link']);
			$items[$key] = $link;
		}
	} 

	return $items;

}

?>