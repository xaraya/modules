<?php
/**
 * Cacher Module
 *
 * @package modules
 * @subpackage cacher
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2018 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main admin GUI function, entry point
 *
 */

function cacher_admin_main()
{
    if (!xarSecurity::check('ManageCacher')) {
        return;
    }

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        $redirect = xarModVars::get('cacher', 'backend_page');
        if (!empty($redirect)) {
            $truecurrenturl = xarServer::getCurrentURL(array(), false);
            $urldata = xarMod::apiFunc('roles', 'user', 'parseuserhome', array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
            xarController::redirect($urldata['redirecturl']);
        } else {
            xarController::redirect(xarController::URL('cacher', 'admin', 'modifyconfig'));
        }
    }
    return true;
}
