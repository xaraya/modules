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

function photoshare_userapi_deleteimage($args)
{
	extract($args);

	if (!isset($imageID)) {
		$msg = xarML('Bad param #(1) for #(2) function #(3)() in module #(4)',
		'imageID', 'userapi', 'deleteeimage', 'Photoshare');
		xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
		return;
	}

	$image = xarModAPIFunc('photoshare', 'user', 'getimages', array('imageID' => $imageID ));
	if (!isset($image)) return;


	// Get database setup
	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();
	$imagesTable = $xartable['photoshare_images'];
	$foldersTable = $xartable['photoshare_folders'];

	$sql = "DELETE FROM $imagesTable
			WHERE ps_id = " . xarVarPrepForStore($imageID);

	$result =& $dbconn->Execute($sql);
	if (!isset($result)) return;

	//remove upload
	$ok = xarModAPIFunc(	'uploads',
							'admin',
							'reject',
							array(	'ulid'=>$image['uploadid']));
	if (!isset($ok)) return;

	unset($result);
	$sql = "UPDATE $imagesTable SET
			ps_position = ps_position - 1
			WHERE ps_parentfolder = " . xarVarPrepForStore($image['parentfolder']) .
			" AND ps_position > " . xarVarPrepForStore($image['position']);
	$result =& $dbconn->Execute($sql);
	if (!isset($result)) return;

	unset($result);
	$sql = "UPDATE $foldersTable
			SET ps_mainimage = NULL
			WHERE ps_mainimage = " . xarVarPrepForStore($imageID);
	$result =& $dbconn->Execute($sql);
	if (!isset($result)) return;

	return true;
}

?>
