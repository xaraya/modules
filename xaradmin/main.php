<?php
/**
 * Main
 *
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/eid/1162
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  
 */
function menutree_admin_main() {
    // Check to see the current user has edit access to the menutree module
    if (!xarSecurityCheck('ReadMenuTree')) return;
    
    $refererinfo =  xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
    $info =  xarRequest::getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule){
		 if(!xarVarFetch('tab',   'str', $data['tab'],   '', XARVAR_NOT_REQUIRED)) {return;}
        return xarTplModule('menutree','admin','overview',$data);
    } else {
        xarResponse::redirect(xarModURL('menutree', 'admin', 'menus'));
        return true;
    }

}

?>