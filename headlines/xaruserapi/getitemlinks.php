<?php

/**
 * utility function to pass individual item links to whoever
 * 
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function headlines_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('OverviewHeadlines', 0)) {
        return $itemlinks;
    } 

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('headlines', 'user', 'get',
                              array('hid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('headlines', 'user', 'view',array('hid' => $itemid)),
                                    'title' => xarML('Display Headline'),
                                    'label' => xarVarPrepForDisplay($item['title']));
    }  
    return $itemlinks;
}

?>