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

function gallery_userapi_count_files($args)
{
    extract($args);

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $files_table = $xartable['gallery_files'];
    $linkage_table = $xartable['gallery_files_linkage'];

    $tables = array($files_table);
    $left_join = array();
    $where = array();
    $bindvars = array();

    if( xarModIsAvailable('security') && empty($security_bypass) )
    {
        $security_def = xarModAPIFunc('security', 'user', 'leftjoin',
            array(
                'modid' => xarModGetIdFromName('gallery'),
                'itemtype' => FILE_ITEMTYPE,
                'itemid' => "$files_table.file_id",
                'level' => isset($level) ? $level : null
            )
        );
        if( count($security_def) > 0 )
        {
            if( !empty($security_def['left_join']) )
                $left_joins[] = " {$security_def['left_join']} ";
            if( !empty($security_def['where']) )
                $where[] = "( {$security_def['where']} )";
        }
    }

    $sql = "
        SELECT COUNT( DISTINCT $files_table.file_id)
    ";
    $sql .= " FROM " . join(', ', $tables);

    if( count($left_join) > 0 )
    {
        $sql .= join(' ', $left_join);
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

    if( !empty($album_id) )
    {
        $sql .= " LEFT JOIN $linkage_table ON $files_table.file_id = $linkage_table.file_id ";
        $where[] = " $linkage_table.album_id = ? ";
        $bindvars[] = $album_id;
    }

    if( count($where) > 0 )
    {
        $sql .= " WHERE " . join(" AND ", $where);
    }

    //$sql .= " GROUP BY $files_table.file_id ";
    $sql .= " ORDER BY created DESC ";

    //var_dump($sql);
    //echo "<hr />";
    $rs =& $dbconn->Execute($sql, $bindvars);
    if( !$rs ) return;

    list($count) = $rs->fields;

    return $count;
}
?>