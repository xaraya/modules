<?php
/**
 * User main function
 *
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Main user function
 *
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 * @return array empty
 */
function release_user_main()
{
    // Xaraya security
    if (!xarSecurity::check('ViewRelease')) {
        return;
    }

    $redirect = xarModVars::get('release', 'frontend_page');
    if (!empty($redirect)) {
        $truecurrenturl = xarServer::getCurrentURL([], false);
        $urldata = xarMod::apiFunc('roles', 'user', 'parseuserhome', ['url'=> $redirect,'truecurrenturl'=>$truecurrenturl]);
        xarController::redirect($urldata['redirecturl']);
    } else {
        if (xarUser::isLoggedIn()) {
            xarController::redirect(xarController::URL('release', 'user', 'view'));
        } else {
            xarController::redirect(xarController::URL('release', 'user', 'frontpage'));
        }
    }
    return true;
}
