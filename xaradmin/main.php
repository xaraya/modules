<?php
/**
 * Main admin function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Registration module
 */
/**
 * the main registration function
 */
function registration_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditRegistration')) return;
    if (xarModGetVar('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('registration', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>