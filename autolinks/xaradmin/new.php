<?php

/**
 * add new item
 */
function autolinks_admin_new()
{

    // Security Check
    if(!xarSecurityCheck('AddAutolinks')) return;

    $data['authid'] = xarSecGenAuthKey();

    // Return the output
    return $data;
}

?>