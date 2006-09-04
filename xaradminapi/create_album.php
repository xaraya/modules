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
function gallery_adminapi_create_album($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $album_table = $xartable['gallery_albums'];

    $album_id = $dbconn->GenID();

    if( empty($uid) ){ $uid = xarUserGetVar('uid'); }

    $fields = array(
        'album_id'
        , 'name'
        , 'display_name'
        , 'description'
        , 'status'
        , 'uid'
        , 'display_order');

    $bindvars = array();
    $using_fields = array();
    $marks = array();
    foreach( $fields as $var => $field )
    {
        if( isset($$var) || isset($$field) )
        {
            $using_fields[] = $field;
            if( !is_numeric($var) )
            {
                $bindvars[] = $$var;
                $marks[] = '?';
            }
            else
            {
                $bindvars[] = $$field;
                $marks[] = '?';
            }
        }
        elseif( $field == 'display_order' )
        {
            $sql = "SELECT MAX(display_order)+1 FROM $album_table";
            $rs = $dbconn->Execute($sql);
            if( !$rs ){ return false; }
            $marks[] = !empty($rs->fields[0]) ? $rs->fields[0] : 1;
            $using_fields[] = $field;
        }
    }

    if( count($using_fields) <= 0  ){ return false; }


    $sql = " INSERT INTO  $album_table ";
    $sql .= '(' . join(', ', $using_fields) . ')';
    $sql .= ' VALUES (' . join(', ', $marks) . ')';

    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ){ return false; }

    return $dbconn->Insert_ID();
}
?>