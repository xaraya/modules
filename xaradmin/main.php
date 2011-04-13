<?php
 /**
 * @package modules
 * @copyright (C) 2002-2010 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage amazonfps
 * @link http://xaraya.com/index.php/release/eid/1169
 * @author potion <ryan@webcommunicate.net>
 */
/**
 *  Main function
 */
function amazonfps_admin_main()
{

	// no reason to share this overview with the viewing public
	if (!xarSecurityCheck('DeleteAmazonFPS',0)) {
		return;
	}
     
	$request = new xarRequest();
    $refererinfo =  xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
    $request = new xarRequest();
	$info =  xarController::$request->getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
	if (xarModVars::get('modules', 'disableoverview') == 0 || $samemodule) {
        return xarTplModule('amazonfps','admin','overview');
    } else {
        xarResponse::redirect(xarModURL('amazonfps', 'admin', 'modifyconfig'));
        return true;
    } 

}

?>
