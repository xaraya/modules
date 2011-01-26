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
 *  Add a date created field to a content type
 */
function content_adminapi_adddatecreated($args) {

	extract($args);

	sys::import('modules.dynamicdata.class.objects.master');

	// First check to see if the object already has a property named publication_date
	$pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));

	$filters = array(
		'where' => 'objectid eq ' . $objectid . ' and name eq \'date_created\''
	);

	$items = $pobject->getItems($filters);

	if (count($items) == 1) {
		return false;
	}

	// Add a date_created field to all content types
	$values = array(
		'name' => 'date_created',
		'label' => 'Date created',
		'objectid' => $objectid,
		'type' => 8,
		'source' => 'dynamic_data',
		'status' => 66,
		'seq' => 255
	);
	$pobject = DataObjectMaster::getObject(array('name' => 'properties'));
	$pobject->setFieldValues($values);
	$pobject->createItem();

	return true;

} 
?>