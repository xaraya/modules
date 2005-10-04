<?php

/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function filemanager_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();

    // get cids for security check in getall
    $fileList = xarModAPIFunc('filemanager','user','db_get_file', array('fileId' => $itemids));

    if (!isset($fileList) || empty($fileList)) {
       return $itemlinks;
    }

    foreach ($itemids as $itemid) {
        if (!isset($fileList[$itemid])) {
            continue;
        }

        $file = $fileList[$itemid];

        $itemlinks[$itemid] = array('url'   => xarModURL('filemanager', 'user', 'download',
                                                         array('fileId' => $file['id'])),
                                    'title' => $file['link']['label'],
                                    'label' => xarVarPrepForDisplay($file['name']));
    }
    return $itemlinks;
}

?>
