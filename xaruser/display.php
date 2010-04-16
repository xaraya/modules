<?php
/**
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage shop Module
 * @link http://www.xaraya.com/index.php/release/eid/1031
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * Display an item
 */
function shop_user_display($args)
{

    if(!xarVarFetch('itemid',   'id', $itemid,   NULL, XARVAR_DONT_SET)) {return;} 

    extract($args);

    if (!empty($objectid)) {
        $itemid = $objectid;
    }

    if (empty($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item id', 'user', 'display', 'shop');
        throw new Exception($msg);
    }

// Make sure user has read privileges for the item
    if (!xarSecurityCheck('ReadShop',1,'Item',$itemid)) return;

  // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    // Get the object definition we'll be working with
    $object = DataObjectMaster::getObject(array('name' => 'shop_products'));

    $data['object'] = $object;

    //We don't really have the item until we call getItem()
    $some_id = $object->getItem(array('itemid' => $itemid));

    //Make sure we got something
    if (!isset($some_id) || $some_id != $itemid) return;

    //Get the property names and values for the item with the getFieldValues() method
    $values = $object->getFieldValues();

    $data['itemid'] = $itemid;
        
    //$values is an associative array of property names and values, so...
    foreach ($values as $name => $value) {
        $data[$name] = xarVarPrepForDisplay($value);
    }

    return xarTplModule('shop','user','product', $data);

}

?>