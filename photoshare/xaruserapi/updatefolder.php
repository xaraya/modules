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

function photoshare_userapi_updatefolder($args)
{
	extract($args);

	if (isset($objectid))
		$folderID = $objectid;

	if (!isset($title)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'title', 'userapi', 'updatefolder', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($description)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'description', 'userapi', 'updatefolder', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($viewTemplate)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'viewtemplate', 'userapi', 'updatefolder', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $foldersTable = $xartable['photoshare_folders'];

	$sql = "UPDATE $foldersTable SET
				ps_title = '" . xarVarPrepForStore($title) . "',
				ps_description = '" . xarVarPrepForStore($description) . "',
				ps_template = '" . xarVarPrepForStore(pnVarPrepForOS($viewTemplate)) . "',
				ps_hideframe = '" . xarVarPrepForStore($hideframe) . "',
				ps_blockfromlist = '" . xarVarPrepForStore($blockfromlist) . "'
			WHERE ps_id = " . xarVarPrepForStore($folderID);

    $result =& $dbconn->Execute($sql);
    if (!$result) return;

	return true;
}

?>
