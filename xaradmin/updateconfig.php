<?php
/**
 * File: $Id$
 *
 * Xaraya POP3 Gateway
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage pop3gateway
 * @author John Cox
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
    if (!xarVarFetch('defaultstatus', 'id', $defaultstatus, 1, XARVAR_NOT_REQUIRED)) return;
    xarModSetVar('pop3gateway', 'mailserver', $mailserver);
    xarModSetVar('pop3gateway', 'mailserverlogin', $mailserverlogin);
    xarModSetVar('pop3gateway', 'mailserverpass', $mailserverpass);
    xarModSetVar('pop3gateway', 'mailserverport', $mailserverport);
    xarModSetVar('pop3gateway', 'importpubtype', $importpubtype);
    xarModSetVar('pop3gateway', 'defaultstatus', $defaultstatus);
    xarModCallHooks('module','updateconfig','pop3gateway', array('module' => 'pop3gateway'));
    xarResponseRedirect(xarModURL('pop3gateway', 'admin', 'modifyconfig'));
    return true;
}
?>