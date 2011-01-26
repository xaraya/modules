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
 *  Add a label field to the content_types object (for upgrades from 0.6.0)
 */
function content_utilapi_upgradepre070($args) {

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');

	// First check to see if the object already has a property named publication_date
	$pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));

	$filters = array(
		'where' => 'objectid eq ' . $objectid . ' and name eq \'label\''
	);

	$items = $pobject->getItems($filters);

	if (count($items) == 0) {

		// Add a label
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

	#####################
	#####################

	$pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));
	$filters = array(
		'where' => 'objectid eq ' . $objectid . ' and name eq \'content_type\''
	);

	$items = $pobject->getItems($filters);
	$item = end($items);
	
	$pobject = DataObjectMaster::getObject(array('name' => 'properties'));
	$pobject->getItem(array('itemid' => $item['id']));
	$values = $pobject->getFieldValues();
	$values['label'] = 'Object Name';

	$pobject->setFieldValues($values);
	$pobject->updateItem(array('itemid' => $item['id']));

	return true;

} 
?>