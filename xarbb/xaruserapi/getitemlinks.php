<?php

/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (1 = forums, 2 = topics)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function xarbb_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();

    if (empty($itemtype)) {
        return $itemlinks;

    // forums
    } elseif ($itemtype == 1) {
         $forums = xarModAPIFunc('xarbb','user','getallforums');
         if (empty($forums)) {
             return $itemlinks;
         }
         foreach ($forums as $forum) {
             if (!in_array($forum['fid'],$itemids)) continue;
             $itemlinks[$forum['fid']] = array('url'   => xarModURL('xarbb', 'user', 'viewforum',
                                                                 array('fid' => $forum['fid'])),
                                               'title' => xarML('View Forum'),
                                               'label' => xarVarPrepForDisplay($forum['fname']));
         }

    // topics
    } elseif ($itemtype == 2) {
         $topics = xarModAPIFunc('xarbb','user','getalltopics',
                                 array('tids' => $itemids));
         if (empty($topics)) {
             return $itemlinks;
         }
         foreach ($topics as $topic) {
             $itemlinks[$topic['tid']] = array('url'   => xarModURL('xarbb', 'user', 'viewtopic',
                                                                 array('tid' => $topic['tid'])),
                                               'title' => xarML('View Topic'),
                                               'label' => xarVarPrepForDisplay($topic['ttitle']));
         }
    }

    return $itemlinks;
}

?>
