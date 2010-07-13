<?php


function downloads_user_getfile($args) {

		if (!xarVarFetch('itemid', 'id', $itemid, NULL, XARVAR_NOT_REQUIRED)) {return;}

		if (!xarSecurityCheck('ReadDownloads',1,'Item',$itemid)) return;

		sys::import('modules.dynamicdata.class.objects.master');

		$object = DataObjectMaster::getObject(array('name' => 'downloads'));
		$object->getItem(array('itemid' => $itemid));
		$filename = $object->properties['filename']->getValue();
		$location = $object->properties['location']->getValue();
		$status = $object->properties['status']->getValue();

		if ((int)$status < 2 && !xarSecurityCheck('EditDownloads',1,'Item',$itemid)) {
			return;
		}

		$location = $location . '/';
		$location = str_replace('//','/', $location);

		xarMod::apiFunc('downloads','user','getfile',array(
			'fullPath' => $location . $filename
			));

		return;

}

?>