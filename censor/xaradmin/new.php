<?php
/**
 * add new item
 */
function censor_admin_new()
{ 
    // Security Check
    if (!xarSecurityCheck('AddCensor')) return;
    $data['createlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey(); 
    // Return the output
    return $data;
} 

?>