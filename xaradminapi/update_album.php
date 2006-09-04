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

*/
function gallery_adminapi_update_album($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $albums_table = $xartable['gallery_albums'];

    /*
        variable => field
    */
    $fields = array('display_name', 'description', 'status', 'display_order');

    $sql = "
        UPDATE $albums_table
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

    /*
        Just return is there is nothing to update
    */
    if( count($using_fields) <= 0 )
        return false;

    $sql .= ' SET ' . join(', ', $using_fields);


    $where = array();

    $where[] = " album_id = ? ";
    $bindvars[] = $album_id;

    if( !empty($where) )
    {
        $sql .= " WHERE " . join(" AND ", $where);
    }

    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    return true;
}
?>