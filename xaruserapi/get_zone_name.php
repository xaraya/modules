<?php
// ----------------------------------------------------------------------
// Copyright (C) 2004: Marc Lutolf (marcinmilan@xaraya.com)
// Purpose of file:  Configuration functions for commerce
// ----------------------------------------------------------------------
//  based on:
//  (c) 2003 XT-Commerce
//  (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
//  (c) 2002-2003 osCommerce (oscommerce.sql,v 1.83); www.oscommerce.com
//  (c) 2003  nextcommerce (nextcommerce.sql,v 1.76 2003/08/25); www.nextcommerce.org
// ----------------------------------------------------------------------

function commerce_userapi_get_zone_name($args) 
{
    extract($args);

    if (!isset($zone_id)) $zone_id = 1;
    $condition = "zone_id = '$zone_id' ";

    // Object = ice_languages
    $objectInfo = xarModApiFunc('dynamicdata','user','getobjectinfo',array('name' => 'ice_zones'));
    
    if (isset($country_id)) 
        $condition .= "AND zone_country_id = '$country_id'";
    
    // Retrieve the items
    $items = xarModApiFunc('dynamicdata','user','getitems', array (
                                'modid'     => $objectInfo['moduleid'],
                                'itemtype'  => $objectInfo['itemtype'],
                                'where'     => $condition 
                            ));
    $items = array_shift($items);
    return $items['name'];
    //return $default_zone; ??
}
 ?>