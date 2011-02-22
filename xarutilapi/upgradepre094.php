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
function content_utilapi_upgradepre094() {

	$object = DataObjectMaster::getObjectList(array('name' => 'content'));
	$items = $object->getItems();

	if (!empty($items)) {
		foreach ($items as $key => $value) {
			$object = DataObjectMaster::getObject(array('name' => 'content'));
			$object->getItem(array('itemid' => $key));
			$path = $object->properties['item_path']->value;
			if (empty($path)) {
				$object->properties['item_path']->setValue('/_'.$key.'_');
				$object->updateItem();
			}
		}
	}

	$dbconn =& xarDB::getConn();
	$tables =& xarDB::getTables();

	$prefix = xarDB::getPrefix();
	$tables['content'] = $prefix . '_content';
	$tables['content_types'] = $prefix . '_content_types';

	// Create tables inside a transaction
	try {
		$charset = xarSystemVars::get(sys::CONFIG, 'DB.Charset');
		$dbconn->begin();

		$index = array('name' => $prefix . '_content_item_path',
                       'fields' => array('path'),
                       'unique' => true
                       );
        $query = xarDBCreateIndex($tables['content'], $index);
        $dbconn->Execute($query);

		// drop this index
		$index = array('name' => $prefix . '_content_content_types',
					   'fields' => array('content_type'),
					   'unique' => true
					   );
		$query = xarDBDropIndex($tables['content_types'], $index);
		$dbconn->Execute($query);

		$dbconn->commit();

	} catch (Exception $e) {
		$dbconn->rollback();
		throw $e;
	}

	return true;
	
}
?>