<?php
// File: $Id$
/*
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */

function multisites_admin_updateadminconfig()
{
    if (!xarVarFetch('itemsperpage', 'int:1:', $itemsperpage, '1')) return;    


    // Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Security
    if (!xarSecurityCheck('AdminMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

     if (!isset($itemsperpage)) {
        $itemsperpage=10;
    }

    xarModSetVar('multisites', 'itemsperpage', $itemsperpage);

    xarResponseRedirect(xarModURL('multisites', 'admin', 'adminconfig'));

    return true;
}
?>
