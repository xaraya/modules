<?php

/**
 * get an array of root categories with links
 *
 * @param $args['ptid'] publication type ID
 * @returns array
// TODO: specify return format
 */
function articles_userapi_getrootcats($args)
{
    extract($args);

    if (!isset($ptid) || !is_numeric($ptid)) {
        return array();
    }

    $cidstring = xarModGetVar('articles', 'mastercids.'.$ptid);
    if (empty($cidstring)) {
        return array();
    } else {
        $rootcats = explode(';',$cidstring);
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
    foreach ($catlist as $cid => $info) {
        $item = array();
        $item['catid'] = $info['cid'];
        $item['cattitle'] = xarVarPrepForDisplay($info['name']);
        $item['catlink'] = xarModURL('articles','user','view',
                                    array('catid' => $info['cid'],
                                          'ptid' => $ptid));
        if ($isfirst) {
            $item['catjoin'] = '';
            $isfirst = 0;
        } else {
            $item['catjoin'] = ' | ';
        }
        $catlinks[] = $item;
    }
    return $catlinks;
}

?>
