<?php
/**
 * Scraper Module
 *
 * @package modules
 * @subpackage scraper
 * @category Third Party Xaraya Module
 * @version 1.0.0
 * @copyright (C) 2019 Luetolf-Carroll GmbH
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Main admin GUI function, entry point
 *
 */

function scraper_admin_main()
{
    if(!xarSecurityCheck('ManageScraper')) return;

    if (xarModVars::get('modules', 'disableoverview') == 0){
        return array();
    } else {
        $redirect = xarModVars::get('scraper','backend_page');
        if (!empty($redirect)) {
            $truecurrenturl = xarServer::getCurrentURL(array(), false);
            $urldata = xarMod::apiFunc('roles','user','parseuserhome',array('url'=> $redirect,'truecurrenturl'=>$truecurrenturl));
            xarController::redirect($urldata['redirecturl']);
        } else {
            xarController::redirect(xarModURL('scraper', 'admin', 'modifyconfig'));
        }
    }
    return true;
}
?>