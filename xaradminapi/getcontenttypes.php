<?php
function content_adminapi_getcontenttypes($args){

	$getlabels = false;

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObjectList(array('name' => 'content_types'));
	$types = $object->getItems();

	$content_types = array();

	foreach ($types as $val) {
		$name = $val['content_type'];
		if ($getlabels) {
			$object = DataObjectMaster::getObject(array('name' => $name));
			if(is_object($object)) {
				$content_types[$name] = $object->label;
			}
		} else {
			$content_types[$name] = $name;
		}
	}

	asort($content_types);

	return $content_types;
	
}
?>