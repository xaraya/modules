<?php
/**
 * Event API functions of Stats module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Stats Module
 * @link http://xaraya.com/index.php/release/34.html
 * @author Frank Besler <frank@besler.net>
 */
/**
 * modify configuration
 */
function stats_admin_modifyconfig()
{
    // Security Check
    if (!xarSecurityCheck('AdminStats')) return;

    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED)) return;

    switch (strtolower($phase)) {
        case 'update':
            if (!xarVarFetch('countadmin', 'checkbox', $countadmin, false, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('excludelist', 'str', $excludelist, '', XARVAR_NOT_REQUIRED)) return;
            // the following call returns an empty startdate, so it will be set wrong later on
            // if (!xarVarFetch('startdate', 'str:1', $startdate, '', XARVAR_NOT_REQUIRED)) return;

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;
            // Update module variables
            xarModSetVar('stats', 'countadmin', $countadmin);
            xarModSetVar('stats', 'excludelist', $excludelist);
            // xarModSetVar('stats', 'startdate', $startdate);

            xarResponseRedirect(xarModURL('stats', 'admin', 'modifyconfig'));
            // Return
            return true;

            break;
        case 'modify':
        default:

            // Quick Data Array
            $data['authid'] = xarSecGenAuthKey();
            $data['updatelabel'] = xarML('Update Users Configuration');

            break;
    }

    return $data;
}

?>