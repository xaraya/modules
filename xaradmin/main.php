<?php
/**
 * Main admin function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Authentication module
 */
/**
 * the main administration function
 */
function authentication_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditAuthentication')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('authentication', 'admin', 'modifyconfig'));
    }
    // success
    return true;
}
?>