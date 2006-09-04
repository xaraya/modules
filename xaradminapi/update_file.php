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
function gallery_adminapi_update_file($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $files_table = $xartable['gallery_files'];

    /*
        variable => field
    */
    $fields = array(
        'name'
        , 'summary'
        , 'file'
        , 'status'
        , 'modified'
        , 'file_type'
        , 'file_size'
        , 'display_order');

    $sql = "
        UPDATE $files_table
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

    $sql .= "
            WHERE
                file_id = ?
    ";
    $bindvars[] = $file_id;

    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    return $dbconn->Insert_ID();
}
?>