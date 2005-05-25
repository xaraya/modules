<?php
/**
 *  Common UI function to wrap common info objects rendering
 *
 * @param string objectname The object which contains the definition (and data) 
 * @author Marcel van der Boom <marcel@xaraya.com>
 *
 */
function commerce_admin_commoninfo_object($args = array() )
{
    if(!xarVarFetch('action', 'str',  $action, NULL, XARVAR_DONT_SET)) {return;}
    if(!xarVarFetch('page',   'int',  $page, 1, XARVAR_NOT_REQUIRED)) {return;}
    if(!xarVarFetch('cID',    'int',  $cID, NULL, XARVAR_DONT_SET)) {return;}
    extract($args);
    
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
        if($objectinfo['name'] == $objectname) {
            $data['itemtype'] = $objectinfo['itemtype'];
            $data['objectlabel'] = xarML($objectinfo['label']); // What sort of effect does this have?
        }
    }
    if($data['itemtype'] =='') {
        // NOT FOUND
        die('ICE object not found!!!');
    }
    return $data;
}
?>