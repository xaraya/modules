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
    Gets all the galleries in the db.
*/
function gallery_userapi_get_albums($args)
{
    extract($args);

    if( !isset($states) )
    {
        $states = array();
    }

    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $album_table = $xartable['gallery_albums'];
    $settings_table = $xartable['gallery_album_settings'];
    $files_table = $xartable['gallery_files'];
    $files_linkage_table = $xartable['gallery_files_linkage'];

    $fields = array();
    $tables = array($album_table);
    $left_joins = array();
    $bindvars = array();
    $where = array();

    /*
        Get data to join with categories module
    */
    if( !empty($cids) && xarModIsAvailable('categories') )
    {
        $categories_def = xarModAPIFunc('categories', 'user', 'leftjoin',
            array(
                'modid' => xarModGetIdFromName('gallery'),
                'itemtype' => ALBUM_ITEMTYPE,
                'iids' => array(),
                'cids' => $cids
            )
        );
        $left_joins[] = " LEFT JOIN {$categories_def['table']} ON {$categories_def['iid']} = $album_table.gallery_id ";
        $where[] = "( {$categories_def['where']} )";
    }

    if( xarModIsAvailable('security') )
    {
        $security_def = xarModAPIFunc('security', 'user', 'leftjoin',
            array(
                'modid' => xarModGetIdFromName('gallery'),
                'itemtype' => ALBUM_ITEMTYPE,
                'itemid' => "$album_table.album_id",
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

    /*
        Start building the SQL query
        We are building this in stages to make it easier to do left join with other modules
    */
    $fields = array(
        "$album_table.album_id"
        , "$album_table.name"
        , "$album_table.display_name"
        , "$album_table.description"
        , "$album_table.status"
        , "$album_table.display_order"
        , "preview_table.file"
        , "$settings_table.files_per_page"
        , "$settings_table.cols_per_page"
        , "$settings_table.file_width"
        , "$settings_table.watermark_id"
        , "$settings_table.file_quality"
        , "$settings_table.show_date"
        , "$settings_table.sort_order"
    );
    $sql = "SELECT " . join(", ", $fields);
    $sql .= " FROM " . join(', ', $tables);

    /*
        Left Joins
    */
    $left_joins[] = " LEFT JOIN $settings_table ON $album_table.album_id = $settings_table.album_id ";
    $left_joins[] = " LEFT JOIN $files_table as preview_table ON $settings_table.preview_file = preview_table.file_id ";
    $sql .= join(' ', $left_joins);

    /*
        Start generating the wheres
    */
    if( !empty($album_id) )
    {
        $where[] = " $album_table.album_id = ? ";
        $bindvars[] = $album_id;
    }
    else if( !empty($album_ids) && is_array($album_ids) )
    {
        $q_marks = array();
        $num = count($album_ids);
        for( $i = 0; $i < $num; $i++ )
            $q_marks[] = '?';
        $where[] = " $album_table.album_id IN ( " . join(", ", $q_marks) . " ) ";
        $bindvars = array_merge($bindvars, $album_ids);
    }

    if( !empty($name) )
    {
        $where[] = " $album_table.name = ? ";
        $bindvars[] = $name;
    }


    if( isset($states) && is_array($states) && count($states) > 0 )
    {
        // Just an easy way to escape strings w/o bindvars
        $quoted_states = array();
        foreach( $states as $key => $state )
            $quoted_states[$key] = $dbconn->qstr($state);

        $states_where = " $album_table.status IN (" . join(', ', $quoted_states) . ')';

        $full_states_where = " ( $states_where OR $album_table.uid = ?  ) ";
        $bindvars[] = xarUserGetVar('uid');
        $where[] = $full_states_where;
    }

    if( !empty($where) )
    {
        $sql .= " WHERE " . join(" AND ", $where);
    }

    //$sql .= " GROUP BY $album_table.album_id";

    /*
        Options for sorting
    */
    //if( empty($sort) ){ $sort = " $album_table.album_id ASC"; }
    if( !empty($sort) )
    {
        if( substr($sort, 0, 6) == 'random' ){ $sort = 'RAND(NOW()) ASC'; }
//        elseif( substr($sort, 0, 8) == 'album_id' ){ $sort = "$file_linkage_table.$sort";  }
        elseif( substr($sort, 0, 4) == 'date' ){ $sort = "$album_table.modified " . xarModGetVar('gallery', 'sort_order'); }
        else{ $sort = "$album_table.$sort"; }

        $sql .= " ORDER BY $sort ";
    }

    if( !empty($numitems) ){ $sql .= 'LIMIT ' . $numitems; }

    /*
        Execute the SQL
    */
    $rs = $dbconn->Execute($sql, $bindvars);
    if( !$rs ) return false;

    /*
        Process the Results
    */
    $i = 0;
    $albums = array();
    while( !$rs->EOF )
    {
        $settings = array();
        list(
            $album_id
            , $name
            , $display_name
            , $desc
            , $state
            , $order
            , $settings['preview_file']
            , $settings['items_per_page']
            , $settings['cols_per_page']
            , $settings['file_width']
            , $settings['watermark_id']
            , $settings['file_quality']
            , $settings['show_date']
            , $settings['sort_order']) = $rs->fields;

        if( empty($state) ){ $state = 'UNKNOWN'; }
        @list($settings['sort_order'], $settings['sort_type']) = explode('|', $settings['sort_order']);

        /*
            Just set some defaults
        */
        if( empty($settings['sort_order']) )
        {
            $settings['sort_order'] = 'file_id';
        }
        if( empty($settings['sort_type']) )
        {
            $settings['sort_type'] = 'ASC';
        }

        $albums[$album_id] = array(
            'album_id'     => $album_id
            , 'name'         => $name
            , 'display_name' => $display_name
            , 'description'  => $desc
            , 'state'        => $state
            , 'num_items'    => xarModAPIFunc('gallery', 'user', 'count_files',
                array('album_id' => $album_id, 'states' => $states)
            )
            , 'order'        => $order
            , 'settings'     => $settings
        );

        $rs->MoveNext();
        $i++;
    }
    $rs->Close();

    return $albums;
}
?>