<?php
/**
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (optional)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function comments_userapi_getitemlinks($args)
{
    extract($args);
    $itemlinks = array();
    if (!xarSecurityCheck('Comments-Read', 0)) {
        return $itemlinks;
    } 
    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('comments', 'user', 'get_multipleall', array('objectid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('comments', 'user', 'display', array('cid' => $itemid)),
            'title' => xarML('Display Comment'),
                // TODO: replace itemid with title
            'label' => xarVarPrepForDisplay($itemid));
    } 
    return $itemlinks;
} 
?>