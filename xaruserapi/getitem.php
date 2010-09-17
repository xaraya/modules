<?php
/**
 
/**
 * 
 */
function menutree_userapi_getitem($args)
{
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