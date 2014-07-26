<?php
/**
 * Calendar Module
 *
 * @package modules
 * @subpackage calendar module
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function calendar_admin_main()
{
    // Xaraya security
    if(!xarSecurityCheck('ManageCalendar')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0){
        return array();
    } else {
        $redirect = xarModVars::get('calendar','defaultbackpage');
        if (!empty($redirect)) {
            $truecurrenturl = xarServer::getCurrentURL(array(), false);
            $urldata = xarMod::apiFunc('roles','user','parseuserhome',array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
            xarController::redirect($urldata['redirecturl']);
        } else {
            xarController::redirect(xarModURL('calendar', 'admin', 'view'));
        }
    }
    return true;
}
?>

