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

function photoshare_userapi_setmainimage($args)
{
	extract($args);

	if (!isset($imageID) && !isset($image)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'imageID', 'userapi', 'moveimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	if (!isset($image))
		$image = xarModAPIFunc('photoshare', 'user', 'getimages', array('imageID' => $imageID));

	// Get database setup
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();
	$foldersTable = $xartable['photoshare_folders'];

	$sql = "UPDATE $foldersTable
			SET ps_mainimage = " . xarVarPrepForStore($image['id']) . "
			WHERE ps_id = " . xarVarPrepForStore($image['parentfolder']);

	$result =& $dbconn->Execute($sql);
    if (!$result) return;

	return true;
}

?>
