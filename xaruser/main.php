<?php
/**
 * crispBB Forum Module
 *
 * @package modules
 * @copyright (C) 2008-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage crispBB Forum Module
 * @link http://xaraya.com/index.php/release/970.html
 * @author crisp <crisp@crispcreations.co.uk>
 *//**
 * Main entry point
 *
 * @author crisp <crisp@crispcreations.co.uk>
 * @return array
 */
function crispbb_user_main()
{
    // Xaraya security
    if (!xarSecurity::check('ReadCrispBB')) {
        return;
    }

    $redirect = xarModVars::get('ledgerar', 'frontend_page');
    if (!empty($redirect)) {
        $truecurrenturl = xarServer::getCurrentURL([], false);
        $urldata = xarMod::apiFunc('roles', 'user', 'parseuserhome', ['url'=> $redirect,'truecurrenturl'=>$truecurrenturl]);
        xarController::redirect($urldata['redirecturl']);
    } else {
        xarController::redirect(xarController::URL('crispbb', 'user', 'forum_index'));
    }
    return true;
}
