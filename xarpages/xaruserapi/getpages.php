<?php

// Get all matching pages.
// TODO: support DD and groups of IDs.
// TODO: do the item counts from here too, making use of where-clause.
// TODO: allow fetch of page ranges.
// TODO: support a 'prune' flag?
// Retrieve the page type details here too
// pid: page ID (optional)
// name: page name
// itemtype: page itemtype
// parent: page parent (0=root page)
// left_range: tree hierarchy, defined by [0]=>lower_bound [1]=>upper_bound
// left_exclude: prune tree hierarchy, defined by [0]=>lower_bound [1]=>upper_bound
// key: string indicates the key used for pages ('pid', 'index', 'name')
// dd_flag: include dynamic data if available (default true)
// count: boolean return just a count of records if true

function xarpages_userapi_getpages($args)
{
    extract($args);

    $xartable =& xarDBGetTables();
    $dbconn =& xarDBGetConn();

    $where = array();
    $bind = array();

    // Default dynamic data retrieval to true.
    if (!isset($dd_flag)) {
        $dd_flag = true;
    }

    // Possible values for the array key. Defaults to index (count incrementing from zero)
    // Note: 'name' may not be unique, but all the others are.
    if (!xarVarValidate('enum:pid:index:name:left:right', $key, true)) {
        $key = 'index';
    }

    if (isset($name)) {
        $where[] = 'xar_name = ?';
        $bind[] = (string)$name;
    }

    if (isset($status)) {
        $where[] = 'xar_status = ?';
        $bind[] = strtoupper($status);
    }

    if (isset($pid)) {
        $where[] = 'xar_pid = ?';
        $bind[] = (int)$pid;
    }

    if (isset($itemtype)) {
        $where[] = 'xar_itemtype = ?';
        $bind[] = (int)$itemtype;
    }

    if (isset($parent)) {
        $where[] = 'xar_parent = ?';
        $bind[] = (int)$parent;
    }

    // Used to retrieve descendants.
    if (isset($left_range) && is_array($left_range)) {
        $where[] = 'xar_left between ? AND ?';
        $bind[] = (int)$left_range[0];
        $bind[] = (int)$left_range[1];
    }

    // Used to prune a single branch of the tree.
    if (isset($left_exclude) && is_array($left_exclude)) {
        $where[] = 'xar_left NOT between ? AND ?';
        $bind[] = (int)$left_exclude[0];
        $bind[] = (int)$left_exclude[1];
    }

    // Used to retrieve ancestors.
    if (isset($wrap_range) && is_numeric($wrap_range)) {
        $where[] = 'xar_left <= ? AND xar_right >= ?';
        $bind[] = (int)$wrap_range;
        $bind[] = (int)$left_range;
    }

    // We may just want a count.
    if (!empty($count)) {
        $count = true;
    } else {
        $count = false;
    }

    // The ordering is important for later processing, since these
    // pages are always represented as an hierarchy.
    if ($count) {
        $query = 'SELECT COUNT(*)';
    } else {
        $query = 'SELECT xar_pid, xar_name, xar_desc,'
            . ' xar_itemtype, xar_parent, xar_left, xar_right,'
            . ' xar_template, xar_status, xar_encode_url, xar_decode_url,'
            . ' xar_theme, xar_function';
    }

    $query .= ' FROM ' . $xartable['xarpages_pages']
        . (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        . ' ORDER BY xar_left ASC';

    $result = $dbconn->execute($query, $bind);
    if (!$result) return;

    if ($count) {
        if ($result->EOF) {
            $pages = 0;
        } else {
            list($pages) = $result->fields;
        }
    } else {
        $index = 0;
        $id2key = array();
        $pages = array();

        // Get all the page type details.
        $pagetypes = xarModAPIfunc(
            'xarpages', 'user', 'gettypes',
            array('key' => 'ptid', 'dd_flag' => $dd_flag)
        );

        while (!$result->EOF) {
            list(
                $pid, $name, $desc, $itemtype,
                $parent_pid, $left, $right,
                $template, $status,
                $encode_url, $decode_url,
                $theme, $function
            ) = $result->fields;

            // Note: ['parent_pid'] is the parent page ID,
            // but ['parent'] is the parent item key in the
            // pages array.
            $id2key[(int)$pid] = $$key;
            if ($key == 'pid') {
                $parent = (int)$parent_pid;
            } else {
                $parent = $id2key[$parent_pid];
            }

            $pages[$$key] = array(
                'pid' => (int)$pid,
                'name' => $name,
                'desc' => $desc,
                'itemtype' => (int)$itemtype,
                'parent' => $parent,
                'parent_pid' => (int)$parent_pid,
                'left' => (int)$left,
                'right' => (int)$right,
                'template' => $template,
                'theme' => $theme,
                'status' => $status,
                'encode_url' => $encode_url,
                'decode_url' => $decode_url,
                'function' => $function,
                'pagetype' => $pagetypes[$itemtype]
            );

            $result->MoveNext();
            $index += 1;
        }

        if ($dd_flag) {
            // Get the DD properties for the page tree.
            $dd_data = xarModAPIfunc('xarpages', 'user', 'getpagedd', array('pages' => $pages));

            // Merge the DD data into the main page tree.
            // TODO: an easier way to merge arrays? This just seems clumsy.
            if (!empty($dd_data)) {
                foreach($dd_data as $key => $data) {
                    $pages[$key]['dd'] = $data;
                }
            }
        }
    }

    return $pages;
}

?>