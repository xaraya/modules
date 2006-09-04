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
function gallery_adminapi_create_file($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $files_table = $xartable['gallery_files'];

    $file_id = $dbconn->GenID();

    /*
        Set some defaults
    */
    if( empty($created) )
    {
        $created = time();
    }
    if( empty($modified) )
    {
        $modified = time();
    }

    if( empty($uid) ){ $uid = xarUserGetVar('uid'); }

    $fields = array('file_id' , 'name', 'summary', 'file', 'status', 'external_id', 'created', 'modified', 'file_type', 'file_size', 'uid', 'display_order');

    $bindvars = array();
    $using_fields = array();
    $marks = array();
    foreach( $fields as $var => $field )
    {
        if( isset($$var) || isset($$field) )
        {
            $using_fields[] = $field;
            $marks[] = '?';
            if( !is_numeric($var) )
            {
                $bindvars[] = $$var;
            }
            else
            {
                $bindvars[] = $$field;
            }
        }
        elseif( $field == 'display_order' )
        {
            $sql = "SELECT MAX(display_order)+1 FROM $files_table";
            $rs = $dbconn->Execute($sql);
            if( !$rs ){ return false; }
            $marks[] = !empty($rs->fields[0]) ? $rs->fields[0] : 1;
            $using_fields[] = $field;
        }

    }

    if( count($using_fields) <= 0  )
    {
        return false;
    }

    $sql = " INSERT INTO  $files_table ";
    $sql .= '(' . join(', ', $using_fields) . ')';
    $sql .= ' VALUES (' . join(', ', $marks) . ')';

    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    return $dbconn->Insert_ID();
}
?>