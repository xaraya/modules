<?php

/**
 * modify configuration
 */
function xarbb_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminxarBB')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'modify':
        default:

            $data['supportshorturls'] = xarModGetVar('xarbb','SupportShortURLs') ? 'checked' : '';

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
            if (!xarVarFetch('topicsperpage','int:1:',$topicsperpage,20,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('supportshorturls','isset', $supportshorturls,0,XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // Update module variables
            xarModSetVar('xarbb', 'hottopic', $hotTopic);
            xarModSetVar('xarbb', 'redhottopic', $redhotTopic);
            xarModSetVar('xarbb', 'topicsperpage', $topicsperpage);
            xarModSetVar('xarbb', 'SupportShortURLs', $supportshorturls);
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