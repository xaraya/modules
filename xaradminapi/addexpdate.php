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
 *  Add an expiration date field to a content type
 */
function content_adminapi_addexpdate($args) {

    extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

    // First check to see if the object already has a property named expiration_date
    $pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));

    $filters = array(
        'where' => 'objectid eq ' . $objectid . ' and name eq \'expiration_date\''
    );

    $items = $pobject->getItems($filters);

    if (count($items) == 1) {
        return false;
    }

    // Add an expiration_date field to all content types
    $values = array(
        'name' => 'expiration_date',
        'label' => 'Expiration Date',
        'objectid' => $objectid,
        'type' => 8,
        'source' => 'dynamic_data',
        'status' => 33, 
        'seq' => 200 // make it the last field
    );
    $pobject = DataObjectMaster::getObject(array('name' => 'properties'));
    $pobject->setFieldValues($values);
    $pobject->createItem();

    return true;

} 
?>