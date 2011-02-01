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
function content_utilapi_upgradepre090() {

	// 1. Add the path field to the content table
	sys::import('xaraya.tableddl');

	try {
		$dbconn =& xarDB::getConn();
		$tables =& xarDB::getTables();

		$prefix = xarDB::getPrefix();
		$tables['content'] = $prefix . '_content';
		$query = xarDBAlterTable($tables['content'], array(
			 'command' => 'add',
			 'field' => 'path',
			 'type' => 'varchar',
			 'size' => 254,
			'null' => false
		 )); 
		$dbconn->Execute($query);
		$dbconn->commit();	

	} catch (Exception $e) {
		$dbconn->rollback();
		throw $e;
	}

	sys::import('modules.dynamicdata.class.objects.master');
	
	// 2. Add the item_path property to the content object
	$object = DataObjectMaster::getObject(array(
				'name' => 'content'
			));
	$objectid = $object->objectid;
	$values = array(
		'name' => 'item_path',
		'label' => 'Path',
		'objectid' => $objectid,
		'type' => 2,
		'source' => $prefix . '_content.path',
		'status' => 33,
		'defaultvalue' => '',
		'seq' => 3 
	);
	$pobject = DataObjectMaster::getObject(array('name' => 'properties'));
	$pobject->setFieldValues($values);
	$pobject->createItem();
	
	// 3. Copy any path_module data in content type objects to the content object; rename path_module properties to item_path
	$object = DataObjectMaster::getObjectList(array('name' => 'content_types'));
	$ctypes = $object->getItems();
	
	if (!empty($ctypes)) {
		foreach ($ctypes as $val) {
			$ctobject = DataObjectMaster::getObject(array('name' => $val['content_type']));
			$objectid = $ctobject->objectid; //save for later
			$properties = array_keys($ctobject->getProperties());

			if (in_array('path_module', $properties)) {
				$ctobject = DataObjectMaster::getObjectList(array('name' => $val['content_type']));
				$items = $ctobject->getItems();
				if (!empty($items)) {
					foreach ($items as $key => $item) {
						$content = DataObjectMaster::getObject(array('name' => 'content'));
						$content->getItem(array('itemid' => $item['itemid']));
						$content->properties['item_path']->setValue($item['path_module']);
						$content->updateItem();
					}
				}

				// rename the path_module properties to item_path
				$pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));
				$filters = array(
					'where' => 'objectid eq ' . $objectid . ' and name eq \'path_module\''
				);
				$items = $pobject->getItems($filters);
				$item = end($items); // if there is more than one record, hopefully the last one is the one that matters?

				$pobject = DataObjectMaster::getObject(array('name' => 'properties'));
				$pobject->getItem(array('itemid' => $item['id']));
				$values = $pobject->getFieldValues();
				$values['name'] = 'item_path';

				$pobject->setFieldValues($values);
				$pobject->updateItem(array('itemid' => $item['id']));

			}
		}
	}

	return true;
	
}
?>