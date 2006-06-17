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

function commerce_userapi_get_language($args)
{
    extract($args);

    // Get the language which matches the locale? strange, but alas.
    // Object = ice_languages
    $objectInfo = xarModApiFunc('dynamicdata','user','getobjectinfo',array('name' => 'ice_languages'));
    $fieldlist = array('id','name','code','image','directory');
    if(!isset($locale)) $locale="en_US";
    // TODO: what are we going to do with the bind vars here?
    $condition ="directory = '$locale'";
    // Retrieve the items
    $items = xarModApiFunc('dynamicdata','user','getitems', array (
                                'modid'     => $objectInfo['moduleid'],
                                'itemtype'  => $objectInfo['itemtype'],
                                'fieldlist' => $fieldlist,
                                'where'     => $condition // Get rid of this, no more dir=whatever things!!
                            ));
    $items = array_shift($items);
    return $items;
}
?>