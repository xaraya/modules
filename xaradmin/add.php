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
	if(!xarVarFetch('location',       'str',    $location,   NULL, XARVAR_DONT_SET)) {return;}

	if (strstr($filename,'.')) {
		$parts = explode('.',$filename);
		$ext = end($parts);
	} else {
		$ext = '';
	}

	$instance = $itemid.':'.$ext.':'.xarUserGetVar('id');
	if (!xarSecurityCheck('AddDownloads',0,'Record',$instance)) {
		return;
	}

	xarMod::apiFunc('downloads','admin','addrecord',array(
		'location' => $location,
		'filename' => $filename,
		'status' => 2
		));
 
    xarResponse::redirect(xarModURL('downloads','admin','files'));
            
    return true; 

}

?>