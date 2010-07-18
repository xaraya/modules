<?php
/**
 * Add a new item
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
 * Create a new downloads item 
 */
function downloads_admin_add()
{

	if(!xarVarFetch('filename',       'str',    $filename,   NULL, XARVAR_DONT_SET)) {return;}
	if(!xarVarFetch('directory',       'str',    $directory,   NULL, XARVAR_DONT_SET)) {return;}

	if (strstr($filename,'.')) {
		$parts = explode('.',$filename);
		$ext = end($parts);
	} else {
		$ext = '';
	}

	$instance = 'All:'.$ext.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('AddDownloads',0,'Record',$instance)) {
		xarResponse::redirect(xarModURL('downloads','admin','files'));
		return;
	}

	$extensions = str_replace(' ','',xarModVars::get('downloads', 'file_extensions'));
	$extensions = explode(',',$extensions);

	if (strstr($filename,'.')) {
		$parts = explode('.',$filename);
		$ext = end($parts);
	} else {
		$ext = '';
	}

	if(!in_array($ext,$extensions)) { 
		xarResponse::redirect(xarModURL('downloads','admin','files',array('msg' => $ext)));
		return;
	}

	$basepath = xarMod::apiFunc('downloads','admin','getbasepath');
	xarMod::apiFunc('downloads','admin','addrecord',array(
		'basepath' => $basepath,
		'directory' => $directory,
		'filename' => $filename,
		'status' => 2
		));
 
    xarResponse::redirect(xarModURL('downloads','admin','files'));
            
    return true; 

}

?>