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
 *   Add a publication status field to a content type
 */
function content_adminapi_addpubstatus($args) {

    extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

    // First check to see if the object already has a property named publication_status
    $pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));

    $filters = array(
        'where' => 'objectid eq ' . $objectid . ' and name eq \'publication_status\''
    );

    $items = $pobject->getItems($filters);

    if (count($items) == 1) {
        return false;
    }

    $config = 'a:3:{s:12:"display_rows";s:1:"0";s:14:"display_layout";s:7:"default";s:22:"initialization_options";s:46:"0,Submitted;1,Rejected;2,Approved;3,Frontpage;";}';

    // Add a publication_status field to all content types
    $values = array(
        'name' => 'publication_status',
        'label' => 'Status',
        'objectid' => $objectid,
        'type' => 6,
        'source' => 'dynamic_data',
        'status' => 33,
        'configuration' => $config,
        'defaultvalue' => 2,
        'seq' => 235 // make it the last field
    );
    $pobject = DataObjectMaster::getObject(array('name' => 'properties'));
    $pobject->setFieldValues($values);
    $pobject->createItem();

    return true;

} 
?>