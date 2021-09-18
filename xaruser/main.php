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
 * Main user GUI function, entry point
 *
 */

function cacher_user_main()
{
    // Security Check
    if (!xarSecurity::check('ReadCacher')) {
        return;
    }

    $redirect = xarModVars::get('cacher', 'frontend_page');
    if (!empty($redirect)) {
        $truecurrenturl = xarServer::getCurrentURL([], false);
        $urldata = xarMod::apiFunc('roles', 'user', 'parseuserhome', ['url'=> $redirect,'truecurrenturl'=>$truecurrenturl]);
        xarController::redirect($urldata['redirecturl']);
    } else {
        xarController::redirect(xarController::URL('cacher', 'user', 'cacher'));
    }
    return true;
}
