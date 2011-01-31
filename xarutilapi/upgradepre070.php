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
function content_utilapi_upgradepre070() {

	// 1. Add a label field to the content_types table
	
	try {
		$dbconn =& xarDB::getConn();
		$tables =& xarDB::getTables();

		$prefix = xarDB::getPrefix();
		$tables['content_types'] = $prefix . '_content_types';
		$query = xarDBAlterTable($tables['content_types'], array(
			 'command' => 'add',
			 'field' => 'label',
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

	// 2. Add a label property to the the content_types object 
	sys::import('modules.dynamicdata.class.objects.master');
	$object = DataObjectMaster::getObject(array(
		'name' => 'content_types'
	));
	$objectid = $object->objectid;

	// does the content_types object already have a property named label?
	$pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));
	$filters = array(
		'where' => 'objectid eq ' . $objectid . ' and name eq \'label\''
	);
	$items = $pobject->getItems($filters);

	if (count($items) == 0) {
		// add the label property
		$values = array(
			'name' => 'label',
			'label' => 'Label',
			'objectid' => $objectid,
			'type' => 2,
			'source' => $prefix . '_content_types.label',
			'status' => 33,
			'defaultvalue' => '',
			'seq' => 999 // make it the last field
		);
		$pobject = DataObjectMaster::getObject(array('name' => 'properties'));
		$pobject->setFieldValues($values);
		$pobject->createItem();

	}

	// 3. Modify the label for the content_type property in the content_types object.  Set it to 'Object Name'.

	// have to get the properties objectlist because we're going to be working with a different property here than in step 2 above
	$pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));
	$filters = array(
		'where' => 'objectid eq ' . $objectid . ' and name eq \'content_type\''
	);
	$items = $pobject->getItems($filters);
	$item = end($items); // if there is more than one record, hopefully the last one is the one that matters?
	
	$pobject = DataObjectMaster::getObject(array('name' => 'properties'));
	$pobject->getItem(array('itemid' => $item['id']));
	$values = $pobject->getFieldValues();
	$values['label'] = 'Object Name';

	$pobject->setFieldValues($values);
	$pobject->updateItem(array('itemid' => $item['id']));

	return true;

} 
?>