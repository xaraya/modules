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

function photoshare_userapi_addimage($args)
{
	extract($args);

	if (!isset($title)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'title', 'userapi', 'addimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($description)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'description', 'userapi', 'addimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($folderID)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'folderid', 'userapi', 'addimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($owner)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'owner', 'userapi', 'addimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($inputfield)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'uploadInputName', 'userapi', 'addimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	$userInfo = xarModAPIFunc('photoshare',
							'user',
							'getuserinfo',
							array('uid'=>$owner));
	if (!isset($userInfo)) return;

	// Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $imagesTable = $xartable['photoshare_images'];

	$nextId = $dbconn->GenId($imagesTable);

	if (xarModGetvar('photoshare', 'useimagedirectory'))
		$uploadtype='file';
	else
		$uploadtype='db';

	$upload = xarModAPIFunc('uploads',
							'user',
							'upload',
							array(	'uploadfile'=>$inputfield,
									'mod'=>'photoshare',
									'modid'=>$nextId,
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

	$newpos = xarModAPIFunc('photoshare',
							'user',
							'getnewposition',
							array(	'folderID'=>$folderID));
	if (!isset($newpos)){
		xarModAPIFunc(	'uploads',
						'admin',
						'reject',
						array(	'ulid'=>$upload['ulid']));
		return;
	}

	$sql = "INSERT INTO $imagesTable (
				ps_id,
				ps_owner,
				ps_title,
				ps_description,
				ps_uploadid,
				ps_parentfolder,
				ps_createddate,
				ps_bytesize,
				ps_position)
			VALUES ("
				.$nextId.","
				.xarVarPrepForStore($owner) . ",'"
				.xarVarPrepForStore($title) . "','"
				.xarVarPrepForStore($description) . "',"
				.xarVarPrepForStore($upload['ulid']) . ","
				.xarVarPrepForStore($folderID) . ",NOW(),"
				.xarVarPrepForStore($upload['filesize']).","
				.xarVarPrepForStore($newpos).')';

    $result =& $dbconn->Execute($sql);
    if (!$result) {
		xarModAPIFunc(	'uploads',
						'admin',
						'reject',
						array(	'ulid'=>$upload['ulid']));
		return;
	}

	$id = $dbconn->PO_Insert_ID($imagesTable, 'ps_id');

	return $id;
}

?>
