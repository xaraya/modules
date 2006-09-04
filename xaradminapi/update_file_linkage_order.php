<?php
/**
 * Gallery
 *
 * @package   Xaraya eXtensible Management System
 * @copyright (C) 2006 by Brian McGilligan
 * @license   New BSD License <http://www.abrasiontechnology.com/index.php/page/7>
 * @link      http://www.abrasiontechnology.com/
 *
 * @subpackage Gallery module
 * @author     Brian McGilligan
 */
/**

    Links files and galleries together removing any old links

*/
function gallery_adminapi_update_file_linkage_order($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $linkage_table = $xartable['gallery_files_linkage'];

	$sql =
	   "UPDATE $linkage_table "
	   . "SET "
	   . "display_order = ? "
	   . "WHERE file_id = ? AND album_id = ? ";
    $rs = $dbconn->Execute($sql, array($display_order, $file_id, $album_id));
    if( !$rs ){ return false; }

    return true;
}
?>