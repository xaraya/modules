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
function gallery_adminapi_link_files($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $linkage_table = $xartable['gallery_files_linkage'];

    foreach( $file_ids as $file_id )
    {
        /*
            First delete all link where gallery_id are not in gallery_ids
            Gallery could be unselected
        */
        if( count($album_ids) > 0 ){
            $ids = join(', ', $album_ids);
            $sql = "DELETE FROM $linkage_table "
                . "WHERE album_id NOT IN ( $ids ) AND file_id = ? ";
        }
        else{
            $sql = "DELETE FROM $linkage_table "
                . " WHERE file_id = ?";
        }
        $rs = $dbconn->Execute($sql, array($file_id));
        if( !$rs ){ return false; }

        foreach( $album_ids as $album_id )
        {
        	/*
        	   Now link where not linked
        	*/
        	$sql = " SELECT * FROM $linkage_table WHERE file_id = ? AND album_id = ? ";
            $rs = $dbconn->Execute($sql, array($file_id, $album_id));
            if( !$rs ){ return false; }

            if( $rs->EOF )
            {
                $sql = "SELECT MAX(display_order)+1 FROM $linkage_table WHERE album_id = $album_id";
                $rs = $dbconn->Execute($sql);
                if( !$rs ){ return false; }
                $display_order = !empty($rs->fields[0]) ? $rs->fields[0] : 1;

                $sql = "INSERT INTO  $linkage_table "
                    . " ( file_id , album_id, display_order ) VALUES ( ?, ?, ? )";
                $bindvars = array($file_id, $album_id, $display_order);
                $rs = $dbconn->Execute($sql, $bindvars);
                if( !$rs ){ return false; }
            }
        }

    }

    return $dbconn->Insert_ID();
}
?>