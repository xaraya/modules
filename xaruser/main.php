<?php
/**
 * Translations module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Volodymyr Metenchuk
 * @author Marc Lutolf <mfl@netspan.ch>
 */

function translations_user_main($args)
{
    // Security Check
    if(!xarSecurityCheck('ReadTranslations')) return;
    if (!xarUser::IsLoggedIn()) return xarResponse::notFound();

    $redirect = xarModVars::get('translations','defaultfrontpage');
    if (!empty($redirect)) {
        $truecurrenturl = xarServer::getCurrentURL(array(), false);
        $urldata = xarModAPIFunc('roles','user','parseuserhome',array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
        xarController::redirect($urldata['redirecturl']);
    } else {
        xarController::redirect(xarModURL('translations', 'user', 'show_status'));
    }
    return true;
}
?>
