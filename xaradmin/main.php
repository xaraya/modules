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
 * the main administration function
 *
 * Redirects to modifyconfig
 *
 * @author mikespub
 * @access public
 * @param no $ parameters
 * @return bool true on success or void on falure
 * @throws XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION'
 */
function keywords_admin_main()
{
    // Security Check
    if (!xarSecurityCheck('EditKeywords')) {
        return;
    }

    if (xarModVars::get('modules', 'disableoverview') == 0) {
        return array();
    } else {
        $redirect = xarModVars::get('keywords', 'defaultbackpage');
        if (!empty($redirect)) {
            $truecurrenturl = xarServer::getCurrentURL(array(), false);
            $urldata = xarMod::apiFunc('roles', 'user', 'parseuserhome', array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
            xarController::redirect($urldata['redirecturl']);
        } else {
            xarController::redirect(xarModURL('keywords', 'admin', 'view'));
        }
    }
    return true;
}
