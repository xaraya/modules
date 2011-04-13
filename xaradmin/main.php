<?php
/**
 * Main admin function, entry point
 * @package modules
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com

 * @subpackage CKEditor Module
 * @link http://www.xaraya.com/index.php/release/eid/1166
 * @author Marc Lutolf <mfl@netspan.ch> and Ryan Walker <ryan@webcommunicate.net>
 */

function ckeditor_admin_main() {

	if(!xarSecurityCheck('AdminCKEditor')) return;

	$request = new xarRequest();
    $refererinfo =  xarController::$request->getInfo(xarServer::getVar('HTTP_REFERER'));
    $request = new xarRequest();
	$info =  xarController::$request->getInfo();
    $samemodule = $info[0] == $refererinfo[0];

	if (xarModVars::get('modules', 'disableoverview') == 0 || $samemodule) {
		xarResponse::Redirect(xarModURL('ckeditor', 'admin', 'overview'));
	} else {
		xarResponse::Redirect(xarModURL('ckeditor', 'admin', 'modifyconfig'));
	}
	// success
	return true;
}
?>