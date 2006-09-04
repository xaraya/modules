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

function gallery_adminapi_update_album_settings($args)
{
    extract($args);

    if( empty($album_id) ) return false;

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $settings_table = $xartable['gallery_album_settings'];

    $sql = " SELECT COUNT(*) FROM $settings_table WHERE album_id = ? ";
    $bindvars = array($album_id);
    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }
    list( $count ) = $rs->fields;

    /*
        variable => field
    */
    $fields = array('show_date', 'files_per_page', 'cols_per_page', 'file_width', 'file_quality', 'preview_file', 'watermark_id', 'sort_order');

    if( $count > 0  )
    {
        $sql = "
            UPDATE $settings_table
        ";

        $bindvars = array();
        $using_fields = array();
        foreach( $fields as $var => $field )
        {
            if( isset($$var) || isset($$field) )
            {
                $using_fields[] = " $field = ? ";
                if( !is_numeric($var) )
                {
                    $bindvars[] = $$var;
                }
                else
                {
                    $bindvars[] = $$field;
                }
            }
        }
        if( count($using_fields) <= 0 )
            return false;

        $sql .= ' SET ' . join(', ', $using_fields);

        $sql .= "
                WHERE
                    album_id = ?
        ";
        $bindvars[] = $album_id;

        $rs = $dbconn->Execute($sql, $bindvars);
        if( !$rs ){ return false; }
    }
    else
    {
        /*
            Data needs to be inserted
        */
       $sql = "
            INSERT INTO $settings_table
                ( album_id, show_date, files_per_page, cols_per_page, file_width, file_quality )
                VALUES ( ?, ?, ?, ?, ?, ? )
        ";
        $bindvars = array($album_id, $show_date, $items_per_page, $cols_per_page, $file_width, $file_quality);

        $rs = $dbconn->Execute($sql, $bindvars);
        if( !$rs ){ return false; }
    }


    return true;
}
?>