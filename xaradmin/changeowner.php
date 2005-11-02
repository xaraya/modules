<?php
/**
    Interface to see who the owner of an item is.

    @param $args array standard xaraya hook params
    
    @return array standard xaraya hook extrainfo
*/
function owner_admin_changeowner($args)
{
    extract($args);

    if( !xarSecurityCheck('ChangeOwner', 0) ) return '';

    // setup vars
    $modid = '';
    if( !empty($extrainfo['module']) )
    {
        $modid = xarModGetIdFromName($extrainfo['module']);
    }
    
    $itemtype = '';    
    if( !empty($extrainfo['itemtype']) )
    {    
        $itemtype = $extrainfo['itemtype'];
    }
    
    $itemid = '';    
    if( !empty($objectid) )
    {    
        $itemid = $objectid;
    }
    
    $args = array(
        'modid'    => $modid, 
        'itemtype' => $itemtype, 
        'itemid'   => $itemid
    );
    $owner = xarModAPIFunc('owner', 'user', 'get', $args);
    
    if( empty($owner) ) return '';
    
    // Get vars ready for the template
    $owner['name'] = xarUserGetVar('name', $owner['uid']);    
    $data['owner'] = $owner;
    
    return $data;
}
?>