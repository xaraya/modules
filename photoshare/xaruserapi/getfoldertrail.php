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
 

function photoshare_userapi_getfoldertrail($args)
{
	extract($args);
	
	    // Argument check
    if (!isset($folderID) || !is_numeric($folderID)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'folder ID', 'user', 'get',
                    'Photoshare');
        xarExceptionSet(XAR_USER_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return false;
    }

	$dbconn =& xarDBGetConn();
	$xartable =& xarDBGetTables();

	$foldersTable  = $xartable['photoshare_folders'];

	$trail = array();

	$tmp = photoshareGetFolderTrailRecursive($dbconn, $foldersTable, $folderID, $trail);
	if (!isset($tmp))
		return;
	else if ($tmp === false)
		return array();
	
	// Add "top" link to trail
	//$trail[] = array( 'id' => -1, 'title' => xarMl('Top'));
	
	return array_reverse($trail);
}

function photoshareGetFolderTrailRecursive(&$dbconn, $foldersTable, $folderID, &$trail)
{
  $sql = "SELECT   ps_parentFolder,
                   ps_title
          FROM     $foldersTable
          WHERE    ps_id = " . xarVarPrepForStore($folderID);

  $result = $dbconn->execute($sql);

  if (!$result) return;

  if ($result->EOF)
    return true;

  $trail[] = array('id'    => $folderID,
                   'title' => $result->fields[1]);

  $result->Close();

  $tmp = photoshareGetFolderTrailRecursive($dbconn, $foldersTable, $result->fields[0], $trail);
  if (!isset($tmp))
  	return;
  else if ($tmp === false)
    return false;
  else
  	return true;
}

?>
