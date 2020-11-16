<?php
/*
 * @package modules
 * @subpackage release
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @link http://xaraya.com/index.php/release/773.html
*/
function release_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditRelease')) {
        return;
    }

    $redirect = xarModVars::get('release', 'backend_page');
    if (!empty($redirect)) {
        $truecurrenturl = xarServer::getCurrentURL(array(), false);
        $urldata = xarMod::apiFunc('roles', 'user', 'parseuserhome', array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
        xarController::redirect($urldata['redirecturl']);
        return true;
    } else {
        xarController::redirect(xarModURL('release', 'admin', 'viewnotes'));
    }
    return true;
}
