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

function gallery_adminapi_delete_album($args)
{
    extract($args);

    if( empty($album_id) ){ return false; }

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $album_table = $xartable['gallery_albums'];
    $settings_table = $xartable['gallery_album_settings'];
    $files_table = $xartable['gallery_files'];
    $linkage_table = $xartable['gallery_files_linkage'];

    $sql = " DELETE FROM $album_table WHERE album_id = ? ";
    $bindvars = array($album_id);
    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    $sql = " DELETE FROM $settings_table WHERE album_id = ? ";
    $bindvars = array($album_id);
    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    /*
        Get all files ids that are only linked to this gallery and remove them
        , $files_table.file
        LEFT JOIN $files_table ON  $files_table.file_id = $linkage_table.file_id
    */
    $sql = "
        SELECT file_id, album_id
        FROM $linkage_table
        GROUP BY file_id
        HAVING Count( album_id ) = 1
    ";
    $bindvars = array();
    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    while( !$rs->EOF )
    {
        list($file_id, $this_album_id) = $rs->fields;
        if( $this_album_id == $album_id )
        {
            xarModAPIFunc('gallery', 'admin', 'delete_file',
                array(
                    'file_id' => $file_id
                )
            );
        }
        $rs->MoveNext();
    }
    $rs->Close();

    return true;
}
?>