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

    $typeitemtype = xarModGetVar('autolinks', 'typeitemtype');
    if (empty($typeitemtype)) {
        xarModSetVar('autolinks', 'typeitemtype', 1);
    }
    
    // Do config hooks for the item types.
    $hooks = xarModCallHooks(
        'module', 'modifyconfig', 'autolinks',
        array('itemtype' => $typeitemtype, 'module' => 'autolinks'));
    $data['hooks'] = $hooks;
    
    return $data;
}

?>