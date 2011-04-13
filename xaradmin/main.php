<?php
/**
 * Messages Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Messages Module
 * @link http://xaraya.com/index.php/release/6.html
 * @author XarayaGeek
 * @author Ryan Walker
 */
function messages_admin_main() {

    if (!xarSecurityCheck('AdminMessages')) return;

	$request = new xarRequest();
    $refererinfo =  xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
    $request = new xarRequest();
	$info =  xarController::$request->getInfo();
    $samemodule = $info[0] == $refererinfo[0];
    
	if (xarModVars::get('modules', 'disableoverview') == 0 || $samemodule) {
		if(!xarVarFetch('tab',   'str', $data['tab'],   '', XARVAR_NOT_REQUIRED)) {return;}
        return xarTplModule('messages','admin','overview',$data);
    } else {
        xarResponse::redirect(xarModURL('messages', 'admin', 'modifyconfig'));
        return true;
    } 

}

?>
