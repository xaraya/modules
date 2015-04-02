<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author mikespub
 */
/**
 * the main administration function
 *
 * It currently redirects to the admin-view function
 * @return bool true on success
 */
function publications_admin_main()
{

    // Security Check
    if (!xarSecurityCheck('EditPublications')) return;

    $redirect = xarModVars::get('publications','backend_page');
    if (!empty($redirect)) {
        $truecurrenturl = xarServer::getCurrentURL(array(), false);
        $urldata = xarMod::apiFunc('roles','user','parseuserhome',array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
        xarController::redirect($urldata['redirecturl']);
        return true;
    } else {
        xarController::redirect(xarModURL('publications', 'admin', 'view'));
    }
    return true;
}

?>