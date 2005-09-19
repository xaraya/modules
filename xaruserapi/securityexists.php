<?php
/**
    Check if a record exists for a xaraya module item

    @param $args['modid']     
    @param $args['itemtype'] (optional)
    @param $args['itemid']
    
    @return boolean true if security exists otherwise false;    
*/
function security_userapi_securityexists($args)
{
    $result = xarModAPIFunc('security', 'user', 'get', $args); 

    if( count($result) > 0 )
        return true;
    
    return false;
}
?>