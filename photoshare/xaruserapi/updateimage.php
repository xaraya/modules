<?php
/**
 * Photoshare by Chris van de Steeg
 * based on Jorn Lind-Nielsen 's photoshare
 * module for PostNuke
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage photoshare
 * @author Chris van de Steeg
 */

function photoshare_userapi_updateimage($args)
{
	extract($args);

	if (!isset($imageID)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'imageID', 'userapi', 'updateimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($title)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'title', 'userapi', 'updateimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($description)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'description', 'userapi', 'updateimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($folderID)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'folderid', 'userapi', 'updateimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($owner)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'owner', 'userapi', 'updateimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($inputfield)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'uploadInputName', 'userapi', 'updateimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	$image = xarModAPIFunc('photoshare', 'user', 'getimages', array('imageID' => $imageID ));
	if (!isset($image)) return;

	$fileInfo = $_FILES[$inputfield];

	if (isset($fileInfo) && ($fileInfo['size'] > 0)) {
		$userInfo = xarModAPIFunc('photoshare',
								'user',
								'getuserinfo',
								array('uid'=>$owner));
		if (!isset($userInfo)) return;

		if (xarModGetvar('photoshare', 'useimagedirectory'))
			$uploadtype='file';
		else
			$uploadtype='db';

		$upload = xarModAPIFunc('uploads',
								'user',
								'upload',
								array(	'uploadfile'=>$inputfield,
										'mod'=>'photoshare',
										'modid'=>$imageID,
										'utype'=>$uploadtype));

		if (!isset($upload)) return;

		$totalCapacityUsed = $userInfo['totalCapacityUsed'];
			// Check upload file size
		$totalCapacityUsed += $upload['filesize'];

		if ($totalCapacityUsed > $userInfo['imageSizeLimitTotal']) {
			xarModAPIFunc(	'uploads',
							'admin',
							'reject',
							array(	'ulid'=>$upload['ulid']));
			xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'INVALID_OPERATION', new SystemException(xarMl('The image cannot be uploaded since it is so big that your storage quota would be exceeded')));
			return;
		}

		//remove old upload
		$ok = xarModAPIFunc(	'uploads',
								'admin',
								'reject',
								array(	'ulid'=>$image['uploadid']));
		if (!isset($ok)) return;
		
		$ulid = $upload['ulid'];
		$bytesize = $upload['filesize'];
	}
	else {
		$ulid = $image['uploadid'];
		$bytesize = $image['bytesize'];
	}

	// Get database setup
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();
	$imagesTable = $xartable['photoshare_images'];

	$sql = "UPDATE $imagesTable SET
				ps_owner = ".xarVarPrepForStore($owner).",
				ps_title = '".xarVarPrepForStore($title)."',
				ps_description = '".xarVarPrepForStore($description)."',
				ps_uploadid = ".xarVarPrepForStore($ulid).",
				ps_parentfolder = ".xarVarPrepForStore($folderID).",
				ps_bytesize = ".xarVarPrepForStore($bytesize)."
			WHERE ps_id = ".xarVarPrepForStore($imageID);

    $result =& $dbconn->Execute($sql);
    if (!$result) {
		if (isset($fileInfo) && ($fileInfo['size'] > 0)) {
			xarModAPIFunc(	'uploads',
							'admin',
							'reject',
							array(	'ulid'=>$upload['ulid']));
		}
		return;
	}

	return true;
}

?>
