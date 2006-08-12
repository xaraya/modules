<?php
/**
 *
 * Function display
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2006 by to be added
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link to be added
 * @subpackage Gmaps Module
 * @author Marc Lutolf <mfl@netspan.ch>
 * @author Brian Bain <xaraya@tefen.net>
 *
 * Purpose of file:  Display a Google Map
 *
 * @param to be added
 * @return to be added
 *
 */

function gmaps_user_main()
{
   // Security Check
    if(!xarSecurityCheck('ReadGmaps')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        xarResponseRedirect(xarModURL('gmaps', 'user', 'display'));
    }
    // success
    return true;
}
?>