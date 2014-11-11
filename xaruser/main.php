<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */

/**
 * display keywords entries
 * @return mixed bool and redirect to url
 */
function keywords_user_main($args=array())
{
    // Xaraya security
    if(!xarSecurityCheck('ReadKeywords')) return;

    $redirect = xarModVars::get('keywords','defaultfrontpage');
    if (!empty($redirect)) {
        $truecurrenturl = xarServer::getCurrentURL(array(), false);
        $urldata = xarMod::apiFunc('roles','user','parseuserhome',array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
        xarController::redirect($urldata['redirecturl']);
    } else {
        xarController::redirect(xarModURL('keywords', 'user', 'view', $args));
    }
    return true;
}

?>