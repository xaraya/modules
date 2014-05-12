<?php
/**
 * Translations Module
 *
 * @package modules
 * @subpackage translations module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/77.html
 * @author Marco Canini
 * @author Marcel van der Boom <marcel@xaraya.com>
 */

/**
 * Entry point for translations admin screen
 *
 * A somewhat longer description of the function which may be 
 * multiple lines, can contain examples.
 *
 * @access  public
 * @return  array template data
*/
function translations_admin_main()
{
    if(!xarSecurityCheck('AdminTranslations')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0){
        return array();
    } else {
        $redirect = xarModVars::get('translations','defaultbackpage');
        if (!empty($redirect)) {
            $truecurrenturl = xarServer::getCurrentURL(array(), false);
            $urldata = xarModAPIFunc('roles','user','parseuserhome',array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
            xarController::redirect($urldata['redirecturl']);
        } else {
            xarController::redirect(xarModURL('translations', 'admin', 'start'));
        }
    }
    return true;
}

?>