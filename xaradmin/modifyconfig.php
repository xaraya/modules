<?php

/**
 * modify configuration
 */
function chat_admin_modifyconfig()
{
    // Security Check
    if(!xarSecurityCheck('AdminChat')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

    switch(strtolower($phase)) {

        case 'modify':
        default:

            $hooks = xarModCallHooks('module', 'modifyconfig', 'chat',
                                    array('module' => 'chat')); // forum
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

            if (!xarVarFetch('server','str:1:',$server,'irc.xaraya.com',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('port','int:1:',$port,6667,XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('channel','str:1:',$channel,'#support',XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
            if (!xarVarFetch('isalias','int:1:',$isalias,0, XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // Update module variables

            xarModVars::set('chat', 'server', $server);
            xarModVars::set('chat', 'port', $port);
            xarModVars::set('chat', 'channel', $channel);
            if (empty($isalias)) {
                xarModVars::set('newsgroups','SupportShortURLs',0);
            } else {
                xarModVars::set('newsgroups','SupportShortURLs',1);
            }

            xarModCallHooks('module','updateconfig','chat',
                           array('module' => 'chat')); // forum
            xarResponse::redirect(xarModURL('chat', 'admin', 'modifyconfig'));
            // Return
            return true;
            break;
    }
    return $data;
}
?>