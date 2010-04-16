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
 *  Get products
 */
function shop_userapi_getproducts($args) 
{
   
   $startnum = 1;

    extract($args);

    if (!xarSecurityCheck('ViewShop')) return;

    if (!isset($items_per_page)) {
        $items_per_page = xarModVars::get('shop','items_per_page');
    }
    $data['items_per_page'] = $items_per_page;

    // Load the DD master object class. This line will likely disappear in future versions
    sys::import('modules.dynamicdata.class.objects.master');
    sys::import('modules.dynamicdata.class.properties.master');

    // Get the object we'll be working with. Note this is a so called object list
    $mylist = DataObjectMaster::getObjectList(array('name' => 'shop_products'));

    $data['sort'] = xarMod::ApiFunc('shop','admin','sort', array(
        //how to sort if the URL doesn't say otherwise...
        'sortfield_fallback' => 'id', 
        'ascdesc_fallback' => 'ASC'
    ));

    // We have some filters for the items
    $filters = array(
                        'startnum' => $startnum,
                     'status'    => DataPropertyMaster::DD_DISPLAYSTATE_ACTIVE,
                    'sort' => $data['sort']
                    );

    if (isset($where)) {
        $filters['where'] = $where;
    }
    
    // Get the items 
    $products = $mylist->getItems($filters);

    // return the products 
    $data['products'] = $products;

    // Return the template variables defined in this function
    return $data;
}

?>