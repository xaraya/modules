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

    $dbconn =& xarDB::getConn();
    $tables =& xarDB::getTables();

    $prefix = xarDB::getPrefix();
    $tables['content'] = $prefix . '_content';
    $tables['content_types'] = $prefix . '_content_types';

    try {

        // before we create the unique index, standardize the name for this column to 'item_path' (in some installs the column may be named 'path')
        try {
            $query = "ALTER TABLE " . $tables['content'] . " CHANGE path item_path varchar(254)";
            // $result will be false if this install has no 'path' column... no problem
            $result = $dbconn->Execute($query); 
        } catch (Exception $e) {
        }
        // make sure source for the item_path property in the content object is correct
        // first get the ID for the content object
        $object = DataObjectMaster::getObject(array('name' => 'content'));
        $objectid = $object->objectid; 
        // get the item_path property...
        $object = DataObjectMaster::getObjectList(array('name' => 'properties'));
        $filters['where'] = 'objectid eq \'' . $objectid . '\' and name eq \'item_path\'';
        $items = $object->getItems($filters); 
        $item = reset($items);
        $object = DataObjectMaster::getObject(array('name' => 'properties'));
        $object->getItem(array('itemid' => $item['id'])); 
        $object->properties['source']->setValue('xar_content.item_path');
        $object->updateItem();

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