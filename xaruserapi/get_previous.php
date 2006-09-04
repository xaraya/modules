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
    Get the next file inrelation to a file id and gallery id

    @param integer $args['file_id']
    @param integer $args['gallery_id']

    @return array

*/
function gallery_userapi_get_previous($args)
{
    extract($args);

    if( empty($file_id) ){ return false; }

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $files_table = $xartable['gallery_files'];
    $file_linkage_table = $xartable['gallery_files_linkage'];

    $tables = array($files_table);
    $left_joins = array();
    $where = array();
    $bindvars = array();

    $fields = array(
        'file_id' => "$files_table.file_id",
        'file'
    );

    if( xarModIsAvailable('security') && empty($security_bypass) )
    {
        $security_def = Security::leftjoin($level,
            xarModGetIdFromName('gallery')
            , FILE_ITEMTYPE
            , "$files_table.file_id");
        if( count($security_def) > 0 )
        {
            if( !empty($security_def['left_join']) )
                $left_joins[] = " {$security_def['left_join']} ";
            if( !empty($security_def['where']) )
                $where[] = "( {$security_def['where']} )";
        }
    }

    $sql = 'SELECT ' . join(', ', $fields);
    $sql .= " FROM " . join(', ', $tables);

    /*
        Left Joins
    */
    $left_joins[] = " LEFT JOIN $file_linkage_table ON $files_table.file_id = $file_linkage_table.file_id ";
    $sql .= join(' ', $left_joins);

    if( !empty($album_id) )
    {
        $where[] = " album_id = ? ";
        $bindvars[] = $album_id;
    }

    if( isset($states) && is_array($states) && count($states) > 0 )
    {
        // Just an easy way to escape strings w/o bindvars
        foreach( $states as $key => $state )
            $states[$key] = $dbconn->qstr($state);

        $states_where = " $files_table.status IN (" . join(', ', $states) . ')';

        // NOTE: Users can always see their own files
        $full_states_where = " ( $states_where OR $files_table.uid = ?  ) ";
        $bindvars[] = xarUserGetVar('uid');
        $where[] = $full_states_where;
    }

    /*
        This needs to match up with how records are sorted so that if we are sorted with something other than the id then we need to exclude all records that are after this record or image
    */
    if( $sort == 'display_order' )
    {
        if( !empty($album_id) ){ $table = $file_linkage_table; }
        else{ $table = $files_table; }
        $where[] = " $table.$sort < ? ";
        $bindvars[] = $file['order'];
    }
    else
    {
        $parts = split(' ', $sort, 2);
        $sort_by = $parts[0];
        if( substr($sort, 0, 6) != 'random' )
        {
            if( substr($sort, 0, 4) == 'date' ){ $sort = str_replace('date', 'modified', $sort); }
            elseif( substr($sort, 0, 8) == 'album_id' ){ $sort = str_replace('album_id', 'file_id', $sort); }
            $where[] = " $files_table.$sort < (SELECT b.$sort FROM $files_table b WHERE b.file_id = $file_id) ";
        }
    }

    /*
        Generate the where clause
    */
    if( count($where) > 0 )
    {
        $sql .= " WHERE " . join(" AND ", $where);
    }

    if( substr($sort, 0, 6) == 'random' ){ $sort = 'RAND(NOW())'; }
    elseif( substr($sort, 0, 8) == 'album_id' ){ $sort = "$file_linkage_table.$sort";  }
    elseif( substr($sort, 0, 13) == 'display_order' and !empty($album_id) ){ $sort = "$file_linkage_table.$sort";  }
    elseif( substr($sort, 0, 4) == 'date' ){ $sort = "$files_table.modified "; }
    else{ $sort = "$files_table.$sort";  }
    $sql .= " ORDER BY $sort DESC LIMIT 1";

    $rs =& $dbconn->Execute($sql, $bindvars);
    if( !$rs ) return;

    if( $rs->EOF ){ return false; }

    $i = 0;
    $file = array();
    foreach( $fields as $name => $field)
    {
        if( is_int($name) )
            $name = $field;
        $file[$name] = $rs->fields[$i];
        $i++;
    }
    return $file;
}
?>