<?php

/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function articles_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();
    if (empty($itemtype)) {
        $itemtype = null;
    }
    // get cids for security check in getall
    $fields = array('aid','title','pubtypeid','cids');
// CHECKME: make sure we don't need other statuses somewhere
    // get approved and frontpage articles only
    $status = array(2, 3);
    $articles = xarModAPIFunc('articles','user','getall',
                             array('aids' => $itemids,
                                   'ptid' => $itemtype,
                                   'fields' => $fields,
                                   'status' => $status,
                                  )
                            );
    if (!isset($articles) || !is_array($articles) || count($articles) == 0) {
       return $itemlinks;
    }

    $itemid2key = array();
    foreach ($articles as $key => $article) {
        $itemid2key[$article['aid']] = $key;
    }
    foreach ($itemids as $itemid) {
        if (!isset($itemid2key[$itemid])) continue;
        $article = $articles[$itemid2key[$itemid]];
        $itemlinks[$itemid] = array('url'   => xarModURL('articles', 'user', 'display',
                                                                 array('aid' => $article['aid'],
                                                                       'ptid' => $article['pubtypeid'])),
                                            'title' => xarML('Display Article'),
                                            'label' => xarVarPrepForDisplay($article['title']));
    }
    return $itemlinks;
}

?>
