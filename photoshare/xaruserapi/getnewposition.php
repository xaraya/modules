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

function photoshare_userapi_getnewposition($args)
{
	extract($args);

	if (isset($objectid))
		$folderID = $objectid;

	if (!isset($folderID)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'folderid', 'userapi', 'getnewposition', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $imagesTable  = $xartable['photoshare_images'];

	$sql = "SELECT MAX(ps_position), COUNT(*) FROM $imagesTable
			WHERE ps_parentfolder = " . xarVarPrepForStore($folderID);

	$result =& $dbconn->Execute($sql);

	if (!isset($result)) return;

	if ($result->EOF)
		$newPosition = 0;
	else
	{
		$count = $result->fields[1];
		if ($count == 0)
			$newPosition = 0;
		else
			$newPosition = intval($result->fields[0]) + 1;
	}

	return $newPosition;
}

?>
