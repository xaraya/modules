<?php
function content_adminapi_getcontenttypes($args){

	$names = false;

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObjectList(array('name' => 'content_types'));
	$types = $object->getItems();

	$content_types = array();

	foreach ($types as $val) {
		$object = DataObjectMaster::getObject(array('name' => $val['content_type']));
		$name = $val['content_type'];
		if(is_object($object)) {
			$content_types[$name] = $object->label;
		}
	}

	asort($content_types);

	return $content_types;
	
}
?>