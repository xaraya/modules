<?php

// Get all matching pages.
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
// tree_contains_name: limit the search to the tree of pages containing the page(s) of the given name
// tree_contains_pid: limit the search to the tree of pages containing the page of the given ID

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
        $where[] = 'tpages.xar_name = ?';
        $bind[] = (string)$name;
    }

    if (isset($status)) {
        $where[] = 'tpages.xar_status = ?';
        $bind[] = strtoupper($status);
    }

    if (isset($pid)) {
        $where[] = 'tpages.xar_pid = ?';
        $bind[] = (int)$pid;
    }

    if (isset($itemtype)) {
        $where[] = 'tpages.xar_itemtype = ?';
        $bind[] = (int)$itemtype;
    }

    if (isset($parent)) {
        $where[] = 'tpages.xar_parent = ?';
        $bind[] = (int)$parent;
    }

    // Used to retrieve descendants.
    if (isset($left_range) && is_array($left_range)) {
        $where[] = 'tpages.xar_left between ? AND ?';
        $bind[] = (int)$left_range[0];
        $bind[] = (int)$left_range[1];
    }

    // Used to prune a single branch of the tree.
    if (isset($left_exclude) && is_array($left_exclude)) {
        $where[] = 'tpages.xar_left NOT between ? AND ?';
        $bind[] = (int)$left_exclude[0];
        $bind[] = (int)$left_exclude[1];
    }

    // Used to retrieve ancestors.
    if (isset($wrap_range) && is_numeric($wrap_range)) {
        $where[] = 'tpages.xar_left <= ? AND tpages.xar_right >= ?';
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
        // The DISTINCT is needed in case use of 'tree_contains_name'
        // matches more than one page with the same name.
        $query = 'SELECT DISTINCT tpages.xar_pid, tpages.xar_name, tpages.xar_desc,'
            . ' tpages.xar_itemtype, tpages.xar_parent, tpages.xar_left, tpages.xar_right,'
            . ' tpages.xar_template, tpages.xar_status, tpages.xar_encode_url, tpages.xar_decode_url,'
            . ' tpages.xar_theme, tpages.xar_function, tpages.xar_page_template';
    }

    $query .= ' FROM ' . $xartable['xarpages_pages'] . ' AS tpages';
    
    // If the request is to fetch a tree that *contains* a particular
    // page, then add the extra sub-queries in here.

    if (!empty($tree_contains_pid) || !empty($tree_contains_name)) {
        // Join to get the member page.
        $query .= ' INNER JOIN ' . $xartable['xarpages_pages'] . ' AS tpages_member';

        if (!empty($tree_contains_pid)) {
            $query .= ' ON tpages_member.xar_pid = ?';
            array_unshift($bind, (int)$tree_contains_pid);
        }

        if (!empty($tree_contains_name)) {
            $query .= ' ON tpages_member.xar_name = ?';
            array_unshift($bind, (string)$tree_contains_name);
        }

        // Join to find the root page of the tree containing the required page.
        $query .= ' INNER JOIN ' . $xartable['xarpages_pages'] . ' AS tpages_root'
            . ' ON tpages_root.xar_left <= tpages_member.xar_left'
            . ' AND tpages_root.xar_right >= tpages_member.xar_right'
            . ' AND tpages.xar_left BETWEEN tpages_root.xar_left AND tpages_root.xar_right'
            . ' AND tpages_root.xar_parent = 0';
    }

    $query .= (!empty($where) ? ' WHERE ' . implode(' AND ', $where) : '')
        . ' ORDER BY tpages.xar_left ASC';

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
                $theme, $function, $page_template
            ) = $result->fields;

            $pid = (int)$pid;

            // Note: ['parent_pid'] is the parent page ID,
            // but ['parent'] is the parent item key in the
            // pages array.
            $id2key[(int)$pid] = $$key;
            if ($key == 'pid') {
                $parent_key = (int)$parent_pid;
            } else {
                if (isset($id2key[$parent_pid])) {
                    $parent_key = $id2key[$parent_pid];
                } else {
                    $parent_key = 0;
                }
            }

            $pages[$$key] = array(
                'pid' => $pid,
                'key' => $$key,
                'name' => $name,
                'desc' => $desc,
                'itemtype' => (int)$itemtype, // deprecated
                'ptid' => (int)$itemtype,
                'parent_key' => $parent_key,
                'parent_pid' => (int)$parent_pid,
                'left' => (int)$left,
                'right' => (int)$right,
                'template' => $template,
                'page_template' => $page_template,
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