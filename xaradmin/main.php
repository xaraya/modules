<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Content Module
 * @link http://www.xaraya.com/index.php/release/eid/1118
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function content_admin_main() {
    // Check to see the current user has edit access to the content module
    if (!xarSecurityCheck('EditContent')) return;

    $refererinfo =  xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
    $info =  xarRequest::getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule){
         if(!xarVarFetch('tab',   'str', $data['tab'],   '', XARVAR_NOT_REQUIRED)) {return;}
        return xarTplModule('content','admin','overview',$data);
    } else {
        xarResponse::redirect(xarModURL('content', 'admin', 'view'));
        return true;
    }
}

?>