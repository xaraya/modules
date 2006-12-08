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
    Gets all the files for a gallery in the db.
*/
function gallery_userapi_get_files($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $files_table = $xartable['gallery_files'];
    $file_linkage_table = $xartable['gallery_files_linkage'];

    $fields = array(
        'file_id' => "$files_table.file_id"
        , 'album_id'
        , 'name'
        , 'summary'
        , 'file'
        , 'file_type'
        , 'file_size'
        , 'created'
        , 'status'
        , 'uid'
        , 'order' => !empty($album_id) ? "$file_linkage_table.display_order" : "$files_table.display_order");

    $tables = array($files_table);
    $left_joins = array();
    $where = array();
    $bindvars = array();

    /*
        If the security module is installed and ready to go, get ready to do a left join
    */
    if( xarModIsAvailable('security') && empty($security_bypass) )
    {
        if( !isset($level) ){ $level = null; }
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

    $sql = "SELECT ". join(', ', $fields);
    $sql .= " FROM " . join(', ', $tables);

    /*
        Left Joins
    */
    $left_joins[] = " LEFT JOIN $file_linkage_table ON $files_table.file_id = $file_linkage_table.file_id ";
    $sql .= join(' ', $left_joins);

    /*
        Start Generating the where
    */
    if( !empty($album_id) )
    {
        $where[] = " album_id = ? ";
        $bindvars[] = $album_id;
    }
    else if( !empty($album_ids) && is_array($album_ids) )
    {
        $q_marks = array();
        $num = count($album_ids);
        for( $i = 0; $i < $num; $i++ )
            $q_marks[] = '?';
        $where[] = " album_id IN ( " . join(", ", $q_marks) . " ) ";
        $bindvars = array_merge($bindvars, $album_ids);
    }

    if( !empty($file_id) )
    {
        $where[] = " $files_table.file_id = ? ";
        $bindvars[] = $file_id;
    }
    else if( !empty($file_ids) && is_array($file_ids) )
    {
        $q_marks = array();
        $num = count($file_ids);
        for( $i = 0; $i < $num; $i++ )
            $q_marks[] = '?';
        $where[] = " $files_table.file_id IN ( " . join(", ", $q_marks) . " ) ";
        $bindvars = array_merge($bindvars, $file_ids);
    }

    if( isset($states) && is_array($states) && count($states) > 0 )
    {
        // Just an easy way to escape strings w/o bindvars
        foreach( $states as $key => $state )
            $states[$key] = $dbconn->qstr($state);

        $states_where = " $files_table.status IN (" . join(', ', $states) . ')';

        $full_states_where = " ( $states_where OR $files_table.uid = ?  ) ";
        $bindvars[] = xarUserGetVar('uid');
        $where[] = $full_states_where;
    }

    if( !empty($external_id) )
    {
        $where[] = ' external_id = ? ';
        $bindvars[] = $external_id;
    }

    if( !empty($uid) )
    {
        $where[] = " $files_table.uid = ? ";
        $bindvars[] = $uid;
    }

    if( count($where) > 0 )
    {
        $sql .= " WHERE " . join(" AND ", $where);
    }

    if( empty($file_id) )
    {
        $sql .= " GROUP BY $files_table.file_id ";
    }

    /*
        Options for sorting
    */
    if( !empty($sort) )
    {
        if( substr($sort, 0, 6) == 'random' ){ $sort = 'RAND(NOW()) ASC'; }
        elseif( substr($sort, 0, 8) == 'album_id' ){ $sort = "$file_linkage_table.$sort";  }
        elseif( substr($sort, 0, 13) == 'display_order' and !empty($album_id) ){ $sort = "$file_linkage_table.$sort";  }
        elseif( substr($sort, 0, 4) == 'date' ){ $sort = "$files_table.modified " . xarModGetVar('gallery', 'sort_order'); }
        else{ $sort = "$files_table.$sort";  }

        $sql .= " ORDER BY $sort ";
    }

    if( isset($numitems) && !isset($startnum) )
        $startnum = 1;

    if( isset($numitems) && is_numeric($numitems) )
    {
        $rs =& $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    }
    else
    {
        $rs =& $dbconn->Execute($sql, $bindvars);
    }
    if( !$rs ) return;

    $j = 0;
    $files = array();
    while( !$rs->EOF )
    {
        $j++;
        $i = 0;
        foreach( $fields as $key => $field )
        {
            if( !is_int($key) )
                $field = $key;
        	$$field = $rs->fields[$i++];

        }
        //list($file_id, $gallery_id, $name, $summary, $file, $created, $status) = $rs->fields;

        if( empty($status) )
            $status = 'UNKNOWN';

        if( !isset($files[$file_id]) )
        {
            $files[$file_id] = array(
                'file_id'      => $file_id
                , 'album_id'   => $album_id
                , 'name'       => $name
                , 'summary'    => $summary
                , 'file'       => $file
                , 'type'       => $file_type
                , 'size'       => $file_size
                , 'created'    => $created
                , 'date'       => xarLocaleGetFormattedDate('long', $created)
                , 'state'      => $status
                , 'order'      => $order
            );
        }

        $files[$file_id]['album_ids'][$album_id] = $album_id;

        $rs->MoveNext();
    }
    //var_dump($j);

    return $files;
}
?>