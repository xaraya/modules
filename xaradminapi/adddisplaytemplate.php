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
function content_adminapi_adddisplaytemplate($args) {

    extract($args);

    sys::import('modules.dynamicdata.class.objects.master');

    // First check to see if the object already has a property named publication_date
    $pobject = DataObjectMaster::getObjectList(array('name' => 'properties'));

    $filters = array(
        'where' => 'objectid eq ' . $objectid . ' and name eq \'display_template\''
    );

    $items = $pobject->getItems($filters);

    if (count($items) == 1) {
        return false;
    }

    $apifunc = 'xarMod::apiFunc(\'content\',\'admin\',\'getdisplaytemplates\',array(\'ctype\' => \'the_ctype\'))';

    $apifunc = str_replace('the_ctype', $ctype, $apifunc);

    $len = strlen($apifunc);

    $config = 'a:3:{s:12:"display_rows";s:1:"0";s:14:"display_layout";s:7:"default";s:23:"initialization_function";s:'.$len.':"'.$apifunc.'";}';

    // Add a pub_date field to all content types
    $values = array(
        'name' => 'display_template',
        'label' => 'Display Template',
        'objectid' => $objectid,
        'type' => 6,
        'source' => 'dynamic_data',
        'status' => 33,
        'configuration' => $config,
        'seq' => 180 // make it the last field
    );
    $pobject = DataObjectMaster::getObject(array('name' => 'properties'));
    $pobject->setFieldValues($values);
    $pobject->createItem();

    return true;

} 
?>