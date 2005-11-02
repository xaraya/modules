<?php
/**
    Logs who create a xaraya item
    
    @param $args array standard xaraya hook params
    
    @return array standard xaraya hook extrainfo
*/
function owner_adminapi_createhook($args)
{
    extract($args);
 
    // setup vars for insertion
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
    
    $uid = xarUserGetVar('uid');

    $ownerArgs = array(
        'modid'    => $modid, 
        'itemtype' => $itemtype, 
        'itemid' => $itemid
    );
    $result = xarModAPIFunc('owner', 'admin', 'create', $ownerArgs);
    if( !$result ) return false;
    
    return $extrainfo;    
}

?>