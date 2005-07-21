<?php

/**
 * utility function to pass individual item links to whoever
 * 
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function shopping_userapi_getitemlinks($args)
{
    $itemlinks = array();

/*  // sample item links per item type
    if (empty($args['itemtype'])) {
        $args['itemtype'] = 1;
    }
    switch ($args['itemtype'])
    {
        case 1: // shopping items
            if (!xarSecurityCheck('ViewShoppingItems', 0)) {
                return $itemlinks;
            }
            $items = xarModAPIFunc('shopping','user','getallitems',
                                   array('...' => $args['itemids']));
            if (empty($items) || count($items) < 1) return $itemlinks;
            foreach ($items as $item) {
                $itemid = $item['iid'];
                $itemlinks[$itemid] = array('url' => xarModURL('shopping', 'user', 'displayitem',
                                                               array('iid' => $itemid)),
                                            'title' => xarML('Display Shopping Item'),
                                            'label' => xarVarPrepForDisplay($item['name']));
            }
            break;
        case 2: // shopping recommendations
            if (!xarSecurityCheck('ViewShoppingRecos', 0)) {
                return $itemlinks;
            }
            $recos = xarModAPIFunc('shopping','user','getallrecos',
                                   array('...' => $args['itemids']));
            if (empty($recos) || count($recos) < 1) return $itemlinks;
            foreach ($recos as $reco) {
                $itemid = $reco['rid'];
                // replace with whatever information is relevant here :)
                $itemlinks[$itemid] = array('url' => xarModURL('shopping', 'user', 'viewrecos',
                                                               array('iid' => $reco['iid1'])),
                                            'title' => xarML('View Shopping Recommendations'),
                                            'label' => xarVarPrepForDisplay($reco['name1']));
            }
            break;
        case 3: // shopping profiles
        case 4: // shopping carts
        case 5: // shopping orders
        case 6: // shopping order details
        default:
            //...
            break;
    }
*/

    return $itemlinks;
} 

?>
