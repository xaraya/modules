<?php
/**
 * Publications Module
 *
 * @package modules
 * @subpackage publications module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) 2012 Netspan AG
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * the main user function
 */
function publications_user_main($args)
{
    # --------------------------------------------------------
#
    # Try getting the id of the default page.
#
    $id = xarModVars::get('publications', 'defaultpage');

    if (!empty($id)) {
        # --------------------------------------------------------
#
        # Get the ID of the translation if required
#
        if (!xarVar::fetch('translate', 'int:1', $translate, 1, xarVar::NOT_REQUIRED)) {
            return;
        }
        return xarController::redirect(xarController::URL('publications', 'user', 'display', ['itemid' => $id,'translate' => $translate]));
    } else {
        # --------------------------------------------------------
#
        # No default page, check for a redirect or just show the view page
#
        $redirect = xarModVars::get('publications', 'frontend_page');
        if (!empty($redirect)) {
            $truecurrenturl = xarServer::getCurrentURL([], false);
            $urldata = xarMod::apiFunc('roles', 'user', 'parseuserhome', ['url'=> $redirect,'truecurrenturl'=>$truecurrenturl]);
            xarController::redirect($urldata['redirecturl']);
            return true;
        } else {
            xarController::redirect(xarController::URL('publications', 'user', 'view'));
        }
        return true;
    }
}
