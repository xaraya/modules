<?php

/**
 * utility function to pass individual item links to whoever
 * 
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function example_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('ViewExample', 0)) {
        return $itemlinks;
    } 

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('example', 'user', 'get',
            array('exid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('example', 'user', 'display',
                array('exid' => $itemid)),
            'title' => xarML('Display Example Item'),
            'label' => xarVarPrepForDisplay($item['name']));
    } 
    return $itemlinks;
} 

?>