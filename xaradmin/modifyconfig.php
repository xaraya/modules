<?php
/**
 * modify configuration
 */
function ephemerids_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminEphemerids')) return;
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}
?>