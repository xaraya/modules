<?php
/**
 * Modify configuration for ckeditor 
 * @package ckeditor
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ckeditor 
 */

function ckeditor_admin_modifyconfig() {

	// Security Check
	if (!xarSecurityCheck('AdminCKEditor')) return;
	if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;

	switch (strtolower($phase)) {
		case 'modify':
			break;

		case 'update':
			// Confirm authorisation code
			if (!xarSecConfirmAuthKey()) return;

			if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, xarModVars::get('ckeditor', 'itemsperpage'), XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
			if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('modulealias', 'checkbox', $useModuleAlias,  xarModVars::get('ckeditor', 'useModuleAlias'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('aliasname', 'str', $aliasname,  xarModVars::get('ckeditor', 'aliasname'), XARVAR_NOT_REQUIRED)) return;
	  
			if (!xarVarFetch('PGRFileManager_rootPath', 'str', $PGRFileManager_rootPath,  xarModVars::get('ckeditor', 'PGRFileManager_rootPath'), XARVAR_NOT_REQUIRED)) return;
			if (!xarVarFetch('PGRFileManager_urlPath', 'str', $PGRFileManager_urlPath,  xarModVars::get('ckeditor', 'PGRFileManager_urlPath'), XARVAR_NOT_REQUIRED)) return;

			xarModVars::set('ckeditor', 'PGRFileManager_rootPath', $PGRFileManager_rootPath);
			xarModVars::set('ckeditor', 'PGRFileManager_urlPath', $PGRFileManager_urlPath);

			xarMod::apiFunc('ckeditor','admin','modifypluginsconfig', array(
				'name' => 'PGRFileManager.rootPath',
				'value' => $PGRFileManager_rootPath
				));

			xarMod::apiFunc('ckeditor','admin','modifypluginsconfig', array(
				'name' => 'PGRFileManager.urlPath',
				'value' => $PGRFileManager_urlPath
				));
	
			xarResponse::Redirect(xarModURL('ckeditor', 'admin', 'modifyconfig'));
			// Return
			return true;
			break;

	}
	$data['authid'] = xarSecGenAuthKey();
	return $data;
}
?>