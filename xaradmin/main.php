<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Menu Tree Module
 * @link http://xaraya.com/index.php/release/66.html
 * @author mikespub <mikespub@xaraya.com>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function menutree_admin_main()
{
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