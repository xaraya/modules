<?php

/**
 * modify configuration
 */
function xarbb_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminxarBB')) return;

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'modify';
    }

    switch(strtolower($phase)) {

        case 'modify':
        default:

            $hooks = xarModCallHooks('module', 'modifyconfig', 'xarbb',
                                    array('module' => 'xarbb',
                                          'itemtype' => 1)); // forum
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }

            $data['authid'] = xarSecGenAuthKey();

            break;

        case 'update':

            if (!xarVarFetch('hottopic','int:1:',$hotTopic,10,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('redhottopic','int:1:',$redhotTopic,20,XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // Update module variables
            xarModSetVar('xarbb', 'hottopic', $hotTopic);
            xarModSetVar('xarbb', 'redhottopic', $redhotTopic);
            xarModCallHooks('module','updateconfig','xarbb',
                           array('module' => 'xarbb',
                                 'itemtype' => 1)); // forum
            xarResponseRedirect(xarModURL('xarbb', 'admin', 'modifyconfig'));

            // Return
            return true;

            break;
    }

    return $data;
}
?>