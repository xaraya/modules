<?php

/**
 * modify configuration
 */
function newsgroups_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminNewsGroups')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch(strtolower($phase)) {

        case 'modify':
        default:

            $hooks = xarModCallHooks('module', 'modifyconfig', 'newsgroups',
                                    array('module' => 'newsgroups',
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

            if (!xarVarFetch('server','str:1:',$server,'news.xaraya.com',XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('port','int:1:',$port,119,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('numitems','int:1:',$itemsperpage,50,XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // Update module variables

            xarModSetVar('newsgroups', 'server', $server);
            xarModSetVar('newsgroups', 'port', $port);
            xarModSetVar('newsgroups', 'numitems', $itemsperpage);

            xarModCallHooks('module','updateconfig','newsgroups',
                           array('module' => 'newsgroups',
                                 'itemtype' => 1)); // forum
            xarResponseRedirect(xarModURL('newsgroups', 'admin', 'modifyconfig'));

            // Return
            return true;

            break;
    }

    return $data;
}
?>