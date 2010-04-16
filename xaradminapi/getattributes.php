<?php
  /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Get all product attributes
 */
function shop_adminapi_getattributes() 
{

    $objectname = 'shop_attributes';

    sys::import('modules.dynamicdata.class.objects.master');

    // Get the object we'll be working with. Note this is a so called object list
    $mylist = DataObjectMaster::getObjectList(array('name' =>  $objectname));

    // We have some filters for the items
    $filters = array(
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
                //  'sort' => 'name DESC' 
                    );
    
    // Get the items 
    $items = $mylist->getItems($filters);

    foreach ($items as $item) { 
        $id = $item['id'];
        $array[$id] = $item['name'] . ' (ID: ' . $item['id'] . ')';
    }
        
    return $array;
}

?>