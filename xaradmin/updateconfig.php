<?php
/**
 * Xaraya POP3 Gateway
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage pop3gateway
 * @author John Cox
 */
/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author John Cox
 * @return bool true
 */
function pop3gateway_admin_updateconfig()
{
    if (!xarSecConfirmAuthKey()) return;
    if(!xarSecurityCheck('AdminPOP3Gateway')) return;
    if (!xarVarFetch('mailserver', 'str:1:', $mailserver, 'mail.example.com')) return;
    if (!xarVarFetch('mailserverlogin', 'str:1:', $mailserverlogin, 'login@example.com')) return;
    if (!xarVarFetch('mailserverpass', 'str:1:', $mailserverpass, 'password')) return;
    if (!xarVarFetch('mailserverport', 'int:1:', $mailserverport, '110', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('importpubtype', 'id', $importpubtype, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultstatus', 'int::', $defaultstatus, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('DeleteMailAfter', 'checkbox', $DeleteMailAfter, 0, XARVAR_NOT_REQUIRED)) return;

    
    xarModSetVar('pop3gateway', 'mailserver', $mailserver);
    xarModSetVar('pop3gateway', 'mailserverlogin', $mailserverlogin);
    xarModSetVar('pop3gateway', 'mailserverpass', $mailserverpass);
    xarModSetVar('pop3gateway', 'mailserverport', $mailserverport);
    xarModSetVar('pop3gateway', 'importpubtype', $importpubtype);
    xarModSetVar('pop3gateway', 'defaultstatus', $defaultstatus);
    xarModSetVar('pop3gateway', 'DeleteMailAfter', $DeleteMailAfter);
    
    xarModCallHooks('module','updateconfig','pop3gateway', array('module' => 'pop3gateway'));
    xarResponseRedirect(xarModURL('pop3gateway', 'admin', 'modifyconfig'));
    return true;
}
?>