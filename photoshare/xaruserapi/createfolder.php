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

function photoshare_userapi_createfolder($args)
{
	extract($args);

	if (!isset($parentFolderID)) {
		$parentFolderID = -1;
	}

	if (!isset($title)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'title', 'userapi', 'createfolder', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($description)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'description', 'userapi', 'createfolder', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($viewTemplate)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'viewtemplate', 'userapi', 'createfolder', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($owner)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'owner', 'userapi', 'createfolder', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $foldersTable = $xartable['photoshare_folders'];

	$nextId = $dbconn->GenId($foldersTable);

	$sql = "INSERT INTO $foldersTable (
				ps_id,
				ps_owner,
				ps_title,
				ps_description,
				ps_template,
				ps_hideframe,
				ps_blockfromlist,
				ps_parentFolder,
				ps_createdDate,
				ps_viewKey)
			VALUES (
				$nextId,
				'" . xarVarPrepForStore($owner) . "',
				'" . xarVarPrepForStore($title) . "',
				'" . xarVarPrepForStore($description) . "',
				'" . xarVarPrepForStore(xarVarPrepForOS($viewTemplate)) . "',
				'" . xarVarPrepForStore($hideframe) . "',
				'" . xarVarPrepForStore($blockfromlist) . "',
				'" . xarVarPrepForStore($parentFolderID) . "',
				NOW(),
				round(rand()*9000000000000 + 1000000000000))";

    $result =& $dbconn->Execute($sql);
    if (!$result) return;

	$id = $dbconn->PO_Insert_ID($foldersTable, 'ps_id');

	return $id;
}

?>
