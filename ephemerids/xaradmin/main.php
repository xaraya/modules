<?php
/**
 * File: $Id$
 * 
 * Ephemerids
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Ephemerids Module
 * @author Volodymyr Metenchuk
*/

function ephemerids_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;
    // we only really need to show the default view (overview in this case)
    if (xarModGetVar('adminpanels', 'overview') == 0){
        return array();
    } else {
        xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));
    }
    // success
    return true;
}
?>