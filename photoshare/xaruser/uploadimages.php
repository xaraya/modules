<?php
/**
 * Photoshare by Jorn Lind-Nielsen (C) 2002.
 *
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Jorn Lind-Nielsen / Chris van de Steeg
 */

function photoshare_user_uploadimages()
{
	if (!xarSecurityCheck('AddPhoto')) return;

	if (!xarVarFetch('fid',			'isset:int',	$folderID,	NULL, XARVAR_POST_ONLY)) {return;}
	if (!xarVarFetch('imagenum',	'isset:int',	$imagenum,	NULL, XARVAR_POST_ONLY)) {return;}

	if (!xarSecurityCheck('AddPhoto', 1, 'item', "all:all:$folderID")) return;
	// Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

	for ($i=0; $i < $imagenum; $i++) {
		if (!xarVarFetch('imagetitle'.$i,		'isset', $title,		NULL, XARVAR_DONT_SET)) {return;}
		if (!xarVarFetch('imagedescription'.$i,	'isset', $description, 	NULL, XARVAR_DONT_SET)) {return;}

		if (isset($title) && $title != '') {
			$photo = xarModAPIFunc(	'photoshare',
									'user',
									'addimage',
									array('folderID'    => $folderID,
										'title'         => $title,
										'description'   => $description,
										'owner'			=> xarUserGetVar('uid'),
										'inputfield'	=> 'imagefile'.$i
										));
			if (!isset($photo)) return;
			unset($title);
		}
	}

	xarResponseRedirect(xarModURL('photoshare', 'user', 'view', array('fid' => $folderID)));
	return true;
}

?>
