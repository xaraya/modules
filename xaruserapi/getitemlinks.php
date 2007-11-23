<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
 * Generate links to given tickets
 *
 * @param array $args
 * @param integer $args['itemtype'] - itemtype of items
 * @param array $args['itemids'] - itemids to get info on
 * @return array with itemlinks (URL, title and label for the link)
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