<?php
/**
 * modify configuration
 */
function censor_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminCensor')) return;
    $data['authid'] = xarSecGenAuthKey();
    $data['submitlabel'] = xarML('Submit');
    return $data;
} 
?>