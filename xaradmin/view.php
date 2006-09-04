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

function gallery_admin_view($args)
{
    if( !xarVarFetch('what',     'str', $what, 'albums', XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('startnum', 'int', $startnum,1, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('numitems', 'int', $numitems,10, XARVAR_NOT_REQUIRED) ){ return false; }
    if( !xarVarFetch('reload',   'str', $reload, null, XARVAR_NOT_REQUIRED) ){ return false; }

    $itemtype = 0;
    if( $what == 'albums' ){ $itemtype = ALBUM_ITEMTYPE; }
    elseif( $what == 'files' ){ $itemtype = FILE_ITEMTYPE; }

    if( !Security::check(SECURITY_ADMIN, 'gallery', $itemtype) ){ return false; }

    $data = array();

    switch( $what )
    {
        case 'albums':

            if( !is_null($reload) )
            {
                if( !xarVarFetch('albums', 'array', $albums, array(), XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('old_order', 'array', $old_order, array(), XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('order', 'array', $order, array(), XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('state', 'str', $state, 'SUBMITTED', XARVAR_NOT_REQUIRED) ){ return false; }

                /*
                    Normalize the order of albums
                // updates all the sort orders or postions
                */
                $positions_used = array();
                asort($order); // End of the list should float to the top when orders have dups
                foreach( $order as $album_id => $position )
                {
                    //if( $position > count($order) ){ $position = }
                    // find next open position
//                    while( isset($positions_used[$position]) )
//                    {
//                        $position = ++$position % ($numitems+$startnum); // count($order);
//                    }
//                    $positions_used[$position] = true;
//                    xarModAPIFunc('gallery', 'admin', 'update_album',
//                        array(
//                            'album_id' => $album_id,
//                            'display_order' => $position
//                        )
//                    );



                    if( $position != $old_order[$album_id] )
                    {
                        xarModAPIFunc('gallery', 'admin', 'update_album',
                            array(
                                'album_id'        => $album_id
                                , 'display_order' => $position
                            )
                        );
                        if( $position < $old_order[$album_id] )
                        {
                            gallery_adminapi_increment_albums($position, $album_id);
                        }
                    }


                }
                gallery_adminapi_norm_albums();

                foreach( $albums as  $album )
                {
                    xarModAPIFunc('gallery', 'admin', 'update_album',
                        array(
                            'album_id' => $album,
                            'status'   => $state
                        )
                    );
                }

            }

            /*
                Get all the galleries
            */
            $albums =& xarModAPIFunc('gallery', 'user', 'get_albums',
                array(
                    'sort' => xarModGetVar('gallery', 'sort') . ' ' . xarModGetVar('gallery', 'sort_order')
                )
            );

            foreach( $albums as $key => $album )
            {
                $albums[$key]['view_link'] = xarModURL('gallery', 'admin', 'view',
                    array(
                        'what' => 'files',
                        'album_id' => $key
                    )
                );

                $albums[$key]['modify_link'] = xarModURL('gallery', 'admin', 'modify',
                    array(
                        'what' => $what,
                        'album_id' => $key
                    )
                );
                $albums[$key]['delete_link'] = xarModURL('gallery', 'admin', 'delete',
                    array(
                        'what' => $what,
                        'album_id' => $key
                    )
                );
            }

            $data['albums'] =& $albums;

            $data['view_link'] =& xarModURL('gallery', 'admin', 'view',
                array(
                    'what' => 'files'
                )
            );

            break;

        case 'files':

            if( !xarVarFetch('album_id', 'int', $album_id, null, XARVAR_NOT_REQUIRED) ){ return false; }
            if( !is_null($reload) )
            {
                if( !xarVarFetch('files', 'array', $files, array(), XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('order', 'array', $order, array(), XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('old_order', 'array', $old_order, array(), XARVAR_NOT_REQUIRED) ){ return false; }
                if( !xarVarFetch('state', 'str', $state, 'SUBMITTED', XARVAR_NOT_REQUIRED) ){ return false; }

                /*
                    Normalize the order of files
                    // updates all the sort orders or postions
                */
                $positions_used = array();
                //asort($order);

                foreach( $order as $file_id => $position )
                {
                // Does not work

                    //if( $position > count($order) ){ $position = }
                    // find next open position
                    /*
                    while( isset($positions_used[$position]) )
                    {
                        $position = ++$position % count($order);
                    }
                    $positions_used[$position] = true;
                    */
                    if( !empty($album_id) )
                    {
                        if( $position != $old_order[$file_id] )
                        {
                            xarModAPIFunc('gallery', 'admin', 'update_file_linkage_order',
                                array(
                                    'album_id'        => $album_id
                                    , 'file_id'       => $file_id
                                    , 'display_order' => $position
                                )
                            );
                            if( $position < $old_order[$file_id] )
                            {
                                gallery_adminapi_increment_linkage($position, $album_id);
                            }
                        }
                    }
                    else
                    {
                        if( $position != $old_order[$file_id] )
                        {
                            xarModAPIFunc('gallery', 'admin', 'update_file',
                                array(
                                    'file_id'         => $file_id
                                    , 'display_order' => $position
                                )
                            );
                            if( $position < $old_order[$file_id] )
                            {
                                gallery_adminapi_increment_files($position);
                            }
                        }
                    }
                }
                if( !empty($album_id) )
                {
                    gallery_adminapi_norm_linkage($album_id);
                }
                else
                {
                    gallery_adminapi_norm_files();
                }

                foreach( $files as $file)
                {
                    xarModAPIFunc('gallery', 'admin', 'update_file',
                        array(
                            'file_id'  => $file
                            , 'status' => $state
                        )
                    );
                }
            }

            /*
                Get all the files
            */
            $params = array('album_id' => $album_id);

            if( !empty($params['album_id']) )
            {
                $data['album'] = xarModAPIFunc('gallery', 'user', 'get_album',
                    array(
                        'album_id' => $params['album_id']
                    )
                );

               // sort by album settings
               $sort = $data['album']['settings']['sort_order'] . ' '
                .  $data['album']['settings']['sort_type'];
            }
            else
            {
                // sort by gallery settings
                $sort = xarModGetVar('gallery', 'sort') . ' ' . xarModGetVar('gallery', 'sort_order');
            }

            $item_count = xarModAPIFunc('gallery', 'user', 'count_files',
                array(
                    'album_id' => $params['album_id']
                )
            );

            $params['startnum'] = $startnum;
            $params['numitems'] = $numitems;
            $params['sort'] = $sort;
            //$params['security_bypass'] = true;
            $files =& xarModAPIFunc('gallery', 'user', 'get_files', $params);
            foreach( $files as $key => $file )
            {
                $files[$key]['view_link'] = xarModURL('gallery', 'user', 'display',
                    array(
                        'what' => $what,
                        'album_id' => $file['album_id'],
                        'file_id' => $key,
                        'theme' => 'print'
                    )
                );

                $files[$key]['modify_link'] = xarModURL('gallery', 'admin', 'modify',
                    array(
                        'what' => $what,
                        'file_id' => $key,
                        'album_id' => $params['album_id']
                    )
                );

                $files[$key]['delete_link'] = xarModURL('gallery', 'admin', 'delete',
                    array(
                        'what' => $what,
                        'file_id' => $key,
                        'album_id' => $params['album_id']
                    )
                );
            }

            $data['album_id'] = $params['album_id'];
            $data['files'] =& $files;

            $data['view_link'] =& xarModURL('gallery', 'admin', 'view',
                array(
                    'what' => 'albums'
                )
            );

            break;

        case 'watermarks':

            $data['watermarks'] = @unserialize(xarModGetVar('gallery', 'watermarks'));

            if( !is_array($data['watermarks']) )
            {
                $data['watermarks'] = array();
            }

            foreach( $data['watermarks'] as $key => $watermark )
            {
                $data['watermarks'][$key]['modify_link'] = xarModURL('gallery', 'admin', 'modify',
                    array(
                        'watermark_id' => $key,
                        'what'         => 'watermarks'
                    )
                );
                $data['watermarks'][$key]['delete_link'] = xarModURL('gallery', 'admin', 'delete',
                    array(
                        'watermark_id' => $key,
                        'what'         => 'watermarks'
                    )
                );

            }

            break;

        default:
            $msg = 'This is a big deal and should never happen!';
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'Missing Var', $msg);
            return false;
    }

    /*
        Common template vars
    */
    $data['startnum'] = $startnum;
    $data['numitems'] = $numitems;
    $data['what']     = $what;
    $data['new_link'] = xarModURL('gallery', 'admin', 'new',
        array(
            'what' => $what
        )
    );

    $data['states'] =& xarModAPIFunc('gallery', 'user', 'get_states');

    if( isset($item_count) )
    {
        $url_template = xarModURL('gallery', 'admin', 'view',
            array(
                'album_id' => !empty($params['album_id']) ? $params['album_id'] : null,
                'startnum' => '%%',
                'what'     => $what
            )
        );
        $data['pager'] = xarTplGetPager($startnum, $item_count, $url_template, $numitems);
    }

    $data['files_path'] = xarModGetVar('gallery', 'file_path');

    //var_dump($data);
    return xarTplModule('gallery', 'admin', 'view', $data, $what);
}

function gallery_adminapi_increment_albums($order)
{
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['gallery_albums'];

    $sql = "UPDATE $table as s, $table as r "
        . "SET s.display_order = s.display_order+1 "
        . "WHERE s.display_order = r.display_order+1 "
        . "AND ? < s.display_order AND s.display_order is not null";
    $result = $dbconn->execute($sql, array($order));
    if( !$result ){ return false; }

    return true;
}

function gallery_adminapi_norm_albums()
{
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['gallery_albums'];

    // finds all files that have duplicate display_orders
    $sql = "SELECT album_id, display_order"
        . " FROM $table WHERE display_order IN ( "
           . " SELECT display_order FROM $table GROUP BY display_order "
           . " HAVING COUNT(display_order) > 1 ) "
        . " ORDER BY display_order ASC";
    $result = $dbconn->execute($sql);
    if( !$result ){ return false; }

    $positions_used = array();
    while( (list($album_id, $order) = $result->fields) != null )
    {
        if( isset($positions_used[$order]) )
        {
            $not_ok = true;
            $check_sql = "SELECT min(s.display_order+1) "
                . "FROM $table as s "
                . "LEFT OUTER JOIN $table as r on s.display_order+1 = r.display_order "
                . "WHERE $order <= s.display_order+1 AND r.display_order is null";

            $rs = $dbconn->execute($check_sql);
            if( !$rs ){ return false; }
            $order = $rs->fields[0];

            xarModAPIFunc('gallery', 'admin', 'update_album',
                array(
                    'album_id'        => $album_id
                    , 'display_order' => $order
                )
            );
        }
        $positions_used[$order] = true;

        $result->MoveNext();
    }

    // Check if display order is being used.

}

function gallery_adminapi_increment_files($order)
{
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['gallery_files'];

    $sql = "UPDATE $table as s, $table as r "
        . "SET s.display_order = s.display_order+1 "
        . "WHERE s.display_order = r.display_order+1 "
        . "AND ? < s.display_order AND s.display_order is not null";
    $result = $dbconn->execute($sql, array($order));
    if( !$result ){ return false; }

    return true;
}

function gallery_adminapi_norm_files()
{
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['gallery_files'];

    // finds all files that have duplicate display_orders
    $sql = "SELECT file_id, display_order"
        . " FROM $table WHERE display_order IN ( "
           . " SELECT display_order FROM $table GROUP BY display_order "
           . " HAVING COUNT(display_order) > 1 ) "
        . " ORDER BY display_order ASC";
    $result = $dbconn->execute($sql);
    if( !$result ){ return false; }

    $positions_used = array();
    while( (list($file_id, $order) = $result->fields) != null )
    {
        if( isset($positions_used[$order]) )
        {
            $not_ok = true;
            $check_sql = "SELECT min(s.display_order+1) "
                . "FROM $table as s "
                . "LEFT OUTER JOIN $table as r on s.display_order+1 = r.display_order "
                . "WHERE $order <= s.display_order+1 AND r.display_order is null";

            $rs = $dbconn->execute($check_sql);
            if( !$rs ){ return false; }
            $order = $rs->fields[0];

            xarModAPIFunc('gallery', 'admin', 'update_file',
                array(
                    'file_id'         => $file_id
                    , 'display_order' => $order
                )
            );
        }
        $positions_used[$order] = true;

        $result->MoveNext();
    }

    // Check if display order is being used.

}

function gallery_adminapi_increment_linkage($order, $album_id)
{
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['gallery_files_linkage'];

    $sql = "UPDATE $table as s, $table as r "
        . "SET s.display_order = s.display_order+1 "
        . "WHERE s.album_id = ? AND r.album_id = ? "
        . "AND s.display_order = r.display_order+1 "
        . "AND ? < s.display_order "
        . "AND s.display_order is not null";

    $result = $dbconn->execute($sql, array($album_id, $album_id, $order));
    if( !$result ){ return false; }

    return true;
}

function gallery_adminapi_norm_linkage($album_id)
{
    $dbconn   =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $table = $xartable['gallery_files_linkage'];

    // finds all files that have duplicate display_orders
    $sql = "SELECT file_id, display_order"
        . " FROM $table "
        . " WHERE display_order IN ( "
           . " SELECT display_order FROM $table WHERE album_id = ? "
           . " GROUP BY display_order HAVING COUNT(display_order) > 1 ) "
        . " AND album_id = ? "
        . " ORDER BY display_order ASC";
    $result = $dbconn->execute($sql, array($album_id, $album_id));
    if( !$result ){ return false; }

    $positions_used = array();
    while( (list($file_id, $order) = $result->fields) != null )
    {
        if( isset($positions_used[$order]) )
        {
            $not_ok = true;
            $check_sql = "SELECT min(l.display_order + 1) as start "
                . "FROM $table as l "
                . "LEFT OUTER JOIN $table as r on l.display_order + 1 = r.display_order "
                . "AND l.album_id = r.album_id "
                . "WHERE ? <= l.display_order+1 AND r.display_order is null AND l.album_id = ?";
            $rs = $dbconn->execute($check_sql, array($order, $album_id));
            if( !$rs ){ return false; }
            $order = $rs->fields[0];
            xarModAPIFunc('gallery', 'admin', 'update_file_linkage_order',
                array(
                    'album_id'        => $album_id
                    , 'file_id'       => $file_id
                    , 'display_order' => $order
                )
            );
        }
        $positions_used[$order] = true;

        $result->MoveNext();
    }

    // Check if display order is being used.

}

?>