<?php
/**
 * Otp Module
 *
 * @package modules
 * @subpackage otp
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2017 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main admin GUI function, entry point
 *
 */

function otp_admin_main()
{
    if (!xarSecurityCheck('ManageOtp')) {
        return;
    }

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        $redirect = xarModVars::get('otp', 'backend_page');
        if (!empty($redirect)) {
            $truecurrenturl = xarServer::getCurrentURL(array(), false);
            $urldata = xarModAPIFunc('roles', 'user', 'parseuserhome', array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
            xarController::redirect($urldata['redirecturl']);
        } else {
            xarController::redirect(xarModURL('otp', 'admin', 'modifyconfig'));
        }
    }
    return true;
}
