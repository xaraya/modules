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

function commerce_admin_tax_classes()
{
    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('page',   'int',  $page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('cID',    'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    
    $localeinfo = xarLocaleGetInfo(xarMLSGetSiteLocale());
    $data['language'] = $localeinfo['lang'] . "_" . $localeinfo['country'];
    
    $data['cInfo'] = isset($cInfo) ? get_object_vars($cInfo) : '';
    $data['page'] = $page;
    $data['action'] = $action;
    
    $data['itemsperpage'] = xarModGetVar('commerce', 'itemsperpage');
    // TODO: get these from the object config in DD
    $data['fieldlist'] = '';
    $data['itemid'] = isset($cId) ? $cId : 1;
    
    // Get the itemtype for the ice object
    // TODO: Move this to a commerce api function with the objectname as param or have
    // 1 function as portal to the object mgmt
    $objects  = xarModApiFunc('dynamicdata','user','getobjects');
    $data['itemtype'] = ''; $data['objectlabel'] = xarML('Unlabelled objects');
    foreach($objects as $objectinfo) {
        if($objectinfo['name'] == 'ice_taxclasses') {
            $data['itemtype'] = $objectinfo['itemtype'];
            $data['objectlabel'] = $objectinfo['label'];
        }
    }
    if($data['itemtype'] =='') {
        // NOT FOUND
        die('ICE object not found!!!');
    }
    return $data;
}
?>