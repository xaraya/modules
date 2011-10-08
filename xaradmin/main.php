<?php
/**
 * Ephemerids Module
 *
 * @package modules
 * @subpackage ephemerids module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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

    if (xarModVars::get('modules', 'disableoverview') == 0){
        return array();
    } else {
        xarController::redirect(xarModURL('ephemerids', 'admin', 'view'));
    }
    // success
    return true;
}
?>