<?php
/*
 * Updates the configuration of an user which categories the user wants to 
 * recieve alerts for.
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage julian
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 */
function julian_userapi_updatesubscriptions($categories, $uid = NULL)
{
    if (is_array($categories)) {
        // remove user var if empty
        if (empty($categories)) {
            xarModDelUserVar('julian', 'alerts');
            return;
        }
        $categories = serialize($categories);
    }
    
    if (xarModSetUserVar('julian', 'alerts', $categories, $uid) !== false) {
        return true;
    } else {
        return;
    }
}
?>