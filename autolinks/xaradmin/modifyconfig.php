<?php

/**
 * modify configuration
 */
function autolinks_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminAutolinks')) {return;}

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