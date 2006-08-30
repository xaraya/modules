<?php
/**
 * Generate links to given tickets
 *
 * @param array $args
 * @param integer $args['itemtype'] - itemtype of items
 * @param array $args['itemids'] - itemids to get info on
 * @return unknown
 */
function helpdesk_userapi_getitemlinks($args)
{
    extract($args);

    // Only supporting tickets for now.
    if( !isset($itemtype) || $itemtype != 1 ){ $itemtype = 1; }
    //if( !isset($itemids) ){ return array(); }

    $items = xarModAPIFunc('helpdesk', 'user', 'gettickets',
        array(
            'itemtype' => $itemtype
            , 'itemids' => !empty($itemids) ? $itemids : null
        )
    );

    $itemlinks = array();
    foreach( $items as $key => $item )
    {
        $itemlinks[$item['ticket_id']] = array(
            'label' => $item['subject'],
            'title' => 'Display ' . $item['subject'],
            'url'   => xarModURL('helpdesk', 'user', 'display',
                array(
                    'tid' => $item['ticket_id']
                )
            )
        );
    }

    return $itemlinks;
}
?>