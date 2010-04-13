<?php

/**
 * Utility function to pass individual item links to whoever.
 * In particular, this is used for hooks configuration.
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @param $args['field'] field to return as label in the list (default 'name')
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function xarpages_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();
    if (empty($itemtype)) {
        $itemtype = null;
    }

    if (empty($field)) {
        $field = 'name';
    }

    $pages = xarMod::apiFunc(
        'xarpages','user','getpages',
        array(
            'itemtype' => $itemtype,
            'pids'     => $itemids,
            'key'      => 'pid',
            'dd_flag'  => false
        )
    );

    if (empty($pages)) {
       return $itemlinks;
    }

    // If we didn't have a list of itemids, return all the pages we found.
    if (empty($itemids)) {
        foreach ($pages as $page) {
            $itemid = $page['pid'];
            if (!isset($page[$field])) continue;
            $itemlinks[$itemid] = array(
                'url' => xarModURL('xarpages', 'user', 'display', array('pid' => $page['pid'])),
                'title' => xarML('Display Page'),
                'label' => xarVarPrepForDisplay($page[$field])
            );
        }
        return $itemlinks;
    }

    // If we had a list of itemids, return only those pages.
    $itemid2key = array();
    foreach ($pages as $key => $page) {
        $itemid2key[$page['pid']] = $key;
    }
    foreach ($itemids as $itemid) {
        if (!isset($itemid2key[$itemid])) continue;
        $page = $pages[$itemid2key[$itemid]];
        if (!isset($page[$field])) continue;
        $itemlinks[$itemid] = array(
            'url'   => xarModURL('xarpages', 'user', 'display', array('pid' => $page['pid'])),
            'title' => xarML('Display Page'),
            'label' => xarVarPrepForDisplay($page[$field])
        );
    }

    return $itemlinks;
}

?>