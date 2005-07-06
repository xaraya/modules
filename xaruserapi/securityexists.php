<?php

/**
    Check if a record exists for a xaraya module item
*/
function security_userapi_securityexists($args)
{
    $result = xarModAPIFunc('security', 'user', 'get', $args); 

    if( count($result) > 0 )
        return true;
    
    return false;
}
?>