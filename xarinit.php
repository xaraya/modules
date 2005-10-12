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
 * Initialization functions
 * Initialise the pop3gateway module
 * This function is only ever called once during the lifetime of a particular
 * module instance
 *
 * @author John Cox
 */
function pop3gateway_init()
{
    xarModSetVar('pop3gateway', 'mailserverport', '110');
    xarModSetVar('pop3gateway', 'mailserver', 'mail.example.com');
    xarModSetVar('pop3gateway', 'mailserverlogin', 'login@example.com');
    xarModSetVar('pop3gateway', 'mailserverpass', 'password');
    // Delete mail after retreival
    xarModSetVar('pop3gateway', 'DeleteMailAfter', 1);
    xarRegisterMask('AdminPOP3Gateway','All','pop3gateway','All','All','ACCESS_ADMIN');
    return true;
}
function pop3gateway_delete()
{
    xarModDelAllVars('headlines');
    xarRemoveMasks('headlines');
    xarRemoveInstances('headlines');
    return true;
}
?>