<?php

/**
 * modify configuration
 */
function autolinks_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminAutolinks')) {return;}

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

    $decoration = array();
    $decoration[''] = xarML('Default');
    $decoration['none'] = xarML('None');
    $decoration['underline'] = xarML('Underline');
    $decoration['overline'] = xarML('Overline');
    $decoration['both'] = xarML('Both');
    $data['decoration'] = $decoration;

    $samples = array();
    $samples[0] = xarML('None');
    $samples[1] = xarML('Results Only');
    $samples[2] = xarML('Samples Only');
    $samples[3] = xarML('Samples and Results');
    $data['samples'] = $samples;
    
    return $data;
}

?>