<?php

// Get all matching pages.
// Retrieve the page type details here too
// itemid: page ID (optional)
// pids: list of page ID (optional)
// name: page name
// itemtype: page itemtype
// parent: page parent (0=root page)
// left_range: tree hierarchy, defined by [0]=>lower_bound [1]=>upper_bound
// left_exclude: prune tree hierarchy, defined by [0]=>lower_bound [1]=>upper_bound
// key: string indicates the key used for pages ('id', 'index', 'name')
// dd_flag: include dynamic data if available (default true)
// count: boolean return just a count of records if true
// tree_contains_name: limit the search to the tree of pages containing the page(s) of the given name
// tree_contains_id: limit the search to the tree of pages containing the page of the given ID
// tree_ancestors: boolean, when fetching trees, will limit to just ancestors (and self) of the page name or ID

function publications_userapi_getpages($args)
{
    extract($args);

    if (!xarVarValidate('enum:id:index:name:left:right', $key, true)) {$key = 'index';}

    // Define if we are looking for the number of pages or the pages themselves
    $count = (empty($count)) ? false : true;

    // Assemble the query
    sys::import('xaraya.structures.query');
    $xartable =& xarDB::getTables();
    $q = new Query();
    $q->addtable($xartable['publications'], 'tpages');
    $q->addtable($xartable['publications_types'], 'pt');
    $q->join('pt.id', 'tpages.pubtype_id');
    if ($count) {
        $q->addfield('COUNT(*)');
    } else {
        $q->setdistinct(true);
        $q->addfield('tpages.id AS id');
        $q->addfield('tpages.name AS name');
        $q->addfield('tpages.title AS title');
        $q->addfield('tpages.pubtype_id AS ptid');
        $q->addfield('tpages.parent_id AS base_id');
        $q->addfield('tpages.sitemap_flag AS sitemap_flag');
        $q->addfield('tpages.menu_flag AS menu_flag');
        $q->addfield('tpages.locale AS locale');
        $q->addfield('tpages.leftpage_id AS leftpage_id');
        $q->addfield('tpages.rightpage_id AS rightpage_id');
        $q->addfield('tpages.parentpage_id AS parentpage');
        $q->addfield('tpages.access AS access');
        $q->addfield('tpages.state AS status');
        $q->addfield('pt.description AS pubtype_name');
    }
    
    if (isset($baseonly)) $q->eq('tpages.parent_id', 0);
    if (isset($name)) $q->eq('tpages.name', (string)$name);
    if (isset($status)) {
        // If a list of statuses have been provided, then select for any of them.
        if (strpos($status, ',') === false) {
            $numeric_status = convert_status($status);
            $q->eq('tpages.state', strtoupper($status));
        } else {
            $statuses = explode(',', strtoupper($status));
            $numeric_statuses = array();
            foreach ($statuses as $stat) $numeric_statuses[] = convert_status($stat);
            $q->in('tpages.state', $numeric_statuses);
        }
    }
    if (isset($id)) {
        $q->eq('tpages.id', (int)$id);
        $where[] = 'tpages.id = ?';
        $bind[] = (int)$id;
    } elseif (!empty($ids)) {
        $addwhere = array();
        foreach ($ids as $myid) {
            if (!empty($myid) && is_numeric($myid)) {
                $addwhere[] = (int)$myid;
            }
        }
        $q->in('tpages.state', $addwhere);
    }
    if (isset($itemtype)) $q->eq('tpages.pubtype_id', (int)$itemtype);
    if (isset($parent)) $q->eq('tpages.parentpage_id', (int)$parent);
    // Used to retrieve descendants.
    if (isset($left_range) && is_array($left_range)) {
        $q->between('tpages.leftpage_id', $left_range);
    }
    // Used to prune a single branch of the tree.
    if (isset($left_exclude) && is_array($left_exclude)) {
        //'tpages.leftpage_id NOT between ? AND ?' - does not work on some databases
        $c[] = $q->plt('tpages.leftpage_id',(int)$left_exclude[0]);
        $c[] = $q->pgt('tpages.leftpage_id',(int)$left_exclude[1]);
        $q->qor($c);
        unset($c);
    }
    // Used to retrieve ancestors.
    if (isset($wrap_range) && is_numeric($wrap_range)) {
        $c[] = $q->ple('tpages.leftpage_id',(int)$wrap_range[0]);
        $c[] = $q->pge('tpages.leftpage_id',(int)$left_range[1]);   // can't be right: this is an array
        $q->qand($c);
        unset($c);
    }    

    // If the request is to fetch a tree that *contains* a particular
    // page, then add the extra sub-queries in here.
    if (!empty($tree_contains_id) || !empty($tree_contains_name)) {
        $q->addtable($xartable['publications'], 'tpages_member');
        
        if (!empty($tree_contains_id)) $q->eq('tpages_member.id', (int)$tree_contains_id);
        if (!empty($tree_contains_name)) $q->eq('tpages_member.name', (int)$tree_contains_name);
        if (!empty($tree_ancestors)) {
            // We don't want the complete tree for the matching pages - just
            // their ancestors. This is useful for checking paths, without
            // fetching complete trees.
            $q->between('tpages_member.leftpage_id', 'expr:tpages.leftpage_id AND tpages.rightpage_id');
        } else {
            // Join to find the root page of the tree containing the required page.
            // This matches the complete tree for the root under the selected page.
            $q->addtable($xartable['publications'], 'tpages_root');
            $q->le('tpages_root.leftpage_id', 'expr:tpages_member.leftpage_id');
            $q->ge('tpages_root.rightpage_id', 'expr:tpages_member.rightpage_id');
            $q->between('tpages.leftpage_id', 'expr:tpages_root.leftpage_id AND tpages_root.rightpage_id');
            $q->eq('tpages_root.parentpage_id', 0);
        }
    }

    // This ordering cannot be changed
    // We want the pages in the order of the hierarchy.
    if(empty($count)) $q->setorder('tpages.leftpage_id', 'ASC');

//    $q->qecho();
    $q->run();
    
    if ($count) {
        $pages = count($q->output());
    } else {
        $index = 0;
        $id2key = array();
        $pages = array();

        // Get all the page type details.
        $pagetypes = xarMod::apiFunc('publications', 'user', 'get_pubtypes',
            array('key' => 'id')
        );

        foreach ($q->output() as $row) {

            $id = (int)$row['id'];

            // At this point check the privileges of the page fetched.
            // To prevent broken trees, if a page is not assessible, prune
            // (ie discard) descendant pages of that page. Descendants will have
            // a left value between the left and right values of the
            // inaccessible page.

            if (!empty($prune_left)) {
                if ($row['leftpage_id'] <= $prune_left) {
                    // The current page is still a descendant of the unprivileged page.
                    continue;
                } else {
                    // We've reached a non-descendant - stop pruning now.
                    $prune_left = 0;
                }
            }

            // JDJ 2008-06-11: now only need ViewPublicationsPage to be able to select the page,
            // but ReadPublicationsPage to actually read it.
            // The lowest privilege will be inherited, so one page with only View privilege
            // will cause all descendent pages to have, at most, view privilege.
            // We still need to fetch full details of these view-only pages, but we must flag
            // then up in some way (status?). Displaying any of these pages would instead just
            // show the 'no privs' page.

            // Define admin access
            sys::import('modules.dynamicdata.class.properties.master');
            $accessproperty = DataPropertyMaster::getProperty(array('name' => 'access'));
            $typename = $pagetypes[$row['ptid']]['name'];
            $args = array(
                'instance' => $row['name'] . ":" . $typename,
                'level' => 800,
            );
            $adminaccess = $accessproperty->check($args);

            $info = unserialize($row['access']);
            if (!empty($info['view_access'])) {
                // Decide whether the current user can create blocks of this type
                $args = array(
                    'module' => 'publications',
                    'component' => 'Page',
                    'instance' => $name . ":" . $typename,
                    'group' => $info['view_access']['group'],
                    'level' => $info['view_access']['level'],
                );
                if (!$accessproperty->check($args)) {
                    // Save the right value. We need to skip all subsequent
                    // pages until we get to a page to the right of this one.
                    // The pages will be in 'left' order, so the descendants
                    // will be contiguous and will immediately follow this page.
                    $prune_left = $rightpage_id;
                    // Don't get this unless you are an admin
                    if (!$adminaccess) continue;
                }
            }

            if (!empty($overview_only_left) && $row['leftpage_id'] <= $overview_only_left) {
                // We have got past the overview-only page, so can reset the flag.
                $overview_only_left = 0;
            }

            if (!empty($info['display_access'])) {
                $args = array(
                    'module' => 'publications',
                    'component' => 'Page',
                    'instance' => $name . ":" . $typename,
                    'group' => $info['display_access']['group'],
                    'level' => $info['display_access']['level'],
                );
                if (!$accessproperty->check($args)) {
                    // We have reached a page that allows only overview access.
                    // Flag all pages with the restricted view until we get past this page.
                    $overview_only_left = $row['rightpage_id'];
                    // Don't get this unless you are an admin
                    if (!$adminaccess) continue;
                }
            }

            if (!xarSecurityCheck('ReadPublications', 0, 'Page', $row['name'] . ':' . $typename, 'publications')) {
                // We have reached a page that allows only overview access.
                // Flag all pages with the restricted view until we get past this page.
                $overview_only_left = $row['rightpage_id'];
            }

            // Note: ['parent_id'] is the parent page ID,
            // but ['parent'] is the parent item key in the
            // pages array.
            $id2key[(int)$id] = $$key;
            if ($key == 'id') {
                $parent_key = (int)$row['parentpage'];
            } else {
                if (isset($id2key[$row['parentpage']])) {
                    $parent_key = $id2key[$row['parentpage']];
                } else {
                    $parent_key = 0;
                }
            }
            $row['key'] = $$key;
            $row['access'] = $info;
            $row['parent_key'] = (int)$parent_key;
            $row['left'] = (int)$row['leftpage_id'];
            $row['right'] = (int)$row['rightpage_id'];
            unset($row['leftpage_id']);
            unset($row['rightpage_id']);
            $pages[$$key] = $row;
            $index += 1;
        }
    }
    return $pages;
}

function convert_status($status)
{
    switch ($status)
    {
        case 'DELETED': return 0;
        case 'INACTIVE': return 1;
        case 'DRAFT': return 2;
        case 'ACTIVE': return 3;
        case 'FRONTPAGE': return 4;
        case 'PLACEHOLDER': return 5;
    }
}

?>