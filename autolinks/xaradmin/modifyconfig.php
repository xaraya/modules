<?php

/**
 * modify configuration
 */
function autolinks_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminAutolinks')) return;

    // Select item values (link decoration).
    $data['decoration'] = array(
        '' => xarML('Default'),
        'none' => xarML('None'),
        'underline' => xarML('Underline'),
        'overline' => xarML('Overline'),
        'underline overline' => xarML('Both')
    );

    $data['submit'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();
    return $data;
}

?>