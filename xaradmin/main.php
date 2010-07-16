<?php
/**
 * Main administration
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage downloads
 * @link http://www.xaraya.com/index.php/release/19741.html
 * @author potion <ryan@webcommunicate.net>
 */
/**
 * the main administration function
 * @param none
 * @return array
 */
function downloads_admin_main()
{
    // Check to see the current user has edit access to the downloads module
    if (!xarSecurityCheck('EditDownloads')) return;

    $refererinfo =  xarRequest::getInfo(xarServer::getVar('HTTP_REFERER'));
    $info =  xarRequest::getInfo();
    $samemodule = $info[0] == $refererinfo[0];

	if(!xarVarFetch('tab',   'str', $data['tab'],   '', XARVAR_NOT_REQUIRED)) {return;}
    
    if (((bool)xarModVars::get('modules', 'disableoverview') == false) || $samemodule){
        return xarTplModule('downloads','admin','overview',$data);
    } else {
        xarResponse::redirect(xarModURL('downloads', 'admin', 'view'));
        return true;
    }
}

?>