<?php
/**
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage content
 * @link http://www.xaraya.com/index.php/release/1015.html
 * @author potion <potion@xaraya.com>
 */ 
 /** 
 * @param $args['file'] location of the .xml file containing the object definition, or
 * @param $args['xml'] XML string containing the object definition
 * @param $args['keepitemid'] (try to) keep the item id of the different items (default false)
 * @param $args['entry'] optional array of external references.
 * @param $args['overwrite']
 */

function content_utilapi_import($args) {
  
	$objectid = xarMod::apiFunc('dynamicdata','util','import',$args);
  
	if (empty($objectid)) return false;

	sys::import('modules.dynamicdata.class.objects.master');

	$object = DataObjectMaster::getObject(array('name' => 'objects'));

	$object->getItem(array('itemid' => $objectid));
	$name = $object->properties['name']->value;
	$label = $object->properties['label']->value;

	$ctobject = DataObjectMaster::getObject(array('name' => 'content_types'));
	$ctobject->properties['label']->setValue($label);
	$ctobject->properties['content_type']->setValue($name);
	$ctobject->properties['model']->setValue('imported');
	$itemid = $ctobject->createItem(array('itemid' => $objectid));

	return array('objectid' => $objectid, 'name' => $name);    
	
}
?>