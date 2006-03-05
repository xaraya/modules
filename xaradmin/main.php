<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * Main admin function
 * @return bool true on success of redirect
 */
function ephemerids_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;
    // we only really need to show the default view (overview in this case)
    xarResponseRedirect(xarModURL('ephemerids', 'admin', 'view'));
    // success
    return true;
}
?>