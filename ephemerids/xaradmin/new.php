<?php
/**
 * Default
 */
function ephemerids_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;
    // TODO: figure out how to get a list of *available* languages
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>