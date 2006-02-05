<?php

/**
 * Original author: Articles developer team
 * get an array of root categories with links
 *
 * @param $args['ptid'] publication type ID
 * @param $args['all'] boolean if we need to return all root categories when
 *                     ptid is empty (default false)
 * @returns array
 * @TODO: specify return format
 */
function courses_userapi_getrootcats($args)
{
    extract($args);

    if (empty($tid) || !is_numeric($tid)) {
        $tid = null;
    }

    // see which root categories we need to handle
    $rootcats = array();
    if (!empty($tid)) {
        $cidstring = xarModGetVar('courses','mastercids.'.$tid);
        if (!empty($cidstring)) {
            $rootcats = explode(';',$cidstring);
        }
    } elseif (empty($all)) {
        $cidstring = xarModGetVar('courses','mastercids');
        if (!empty($cidstring)) {
            $rootcats = explode(';',$cidstring);
        }
    } else {
        // Get publication types
        $pubtypes = xarModAPIFunc('courses','user','getpubtypes');
        // get base categories for all publication types here
        $publist = array_keys($pubtypes);
        // add the defaults too, in case we have other base categories there
        $publist[] = '';
        // build the list of root categories for all required publication types
        $catlist = array();
        foreach ($publist as $pubid) {
            if (empty($pubid)) {
                $cidstring = xarModGetVar('courses','mastercids');
            } else {
                $cidstring = xarModGetVar('courses','mastercids.'.$pubid);
            }
            if (!empty($cidstring)) {
                $rootcats = explode(';',$cidstring);
                foreach ($rootcats as $cid) {
                    $catlist[$cid] = 1;
                }
            }
        }
        if (count($catlist) > 0) {
            $rootcats = array_keys($catlist);
        }
    }

    if (count($rootcats) < 1) {
        return array();
    }

    if (!xarModAPILoad('categories', 'user')) return;

    $isfirst = 1;
    $catlinks = array();
    $catlist = xarModAPIFunc('categories',
                            'user',
                            'getcatinfo',
                            array('cids' => $rootcats));
    if (empty($catlist)) {
        return $catlinks;
    }
    // preserve order of root categories if possible
    foreach ($rootcats as $cid) {
        if (!isset($catlist[$cid])) continue;
        $info = $catlist[$cid];
        $item = array();
        $item['catid'] = $info['cid'];
        $item['cattitle'] = xarVarPrepForDisplay($info['name']);
        $item['catlink'] = xarModURL('courses','user','view',
                                    array('ptid' => $tid,
                                          'catid' => $info['cid']));
        if ($isfirst) {
            $item['catjoin'] = '';
            $isfirst = 0;
        } else {
            $item['catjoin'] = ' | ';
        }
        $item['catleft'] = $info['left'];
        $item['catright'] = $info['right'];
        $catlinks[] = $item;
    }
    return $catlinks;
}

?>
