<?php
/**
 *
 * Function display
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Maps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 * @author Brian Bain <xaraya@tefen.net>
 *
 * Purpose of file:  Display a Map
 *
 * @param to be added
 * @return to be added
 *
 */

function maps_user_main()
{
   // Security Check
    if(!xarSecurityCheck('ReadMaps')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('maps', 'user', 'display'));
    }
    // success
    return true;
}
?>