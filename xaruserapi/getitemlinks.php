<?php

/**
 * utility function to pass individual item links to whoever
 * 
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function sitecloud_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('Overviewsitecloud', 0)) {
        return $itemlinks;
    } 

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('sitecloud', 'user', 'get',
            array('id' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('sitecloud', 'user', 'view',
                array('id' => $itemid)),
            'title' => xarML('Display Link'),
            'label' => xarVarPrepForDisplay($item['title']));
    } 
    return $itemlinks;
} 
?>