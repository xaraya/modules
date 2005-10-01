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
function pop3gateway_init()
{
    xarModSetVar('pop3gateway', 'mailserverport', '110');
    xarModSetVar('pop3gateway', 'mailserver', 'mail.example.com');
    xarModSetVar('pop3gateway', 'mailserverlogin', 'login@example.com');
    xarModSetVar('pop3gateway', 'mailserverpass', 'password');
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