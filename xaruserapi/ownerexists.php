<?php
/**
    Check if a record exists for a xaraya module item
    
    @param $args['modid'] (required)
    @param $args['itemtype'] (optional)
    @param $args['itemid'] (required)
    
    @return boolean true if exists otherwise false
*/
function owner_userapi_ownerexists($args)
{
    $result = xarModAPIFunc('owner', 'user', 'get', $args); 
    
    if( count($result) > 0 )
        return true;
    
    return false;
}
?>