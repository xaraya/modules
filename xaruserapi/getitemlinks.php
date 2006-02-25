<?php
/**
 * Utility function to pass individual item links to whoever
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
 /**
 * Utility function to pass individual item links to whoever
 *
 * @author MichelV <michelv@xaraya.com>
 * @param  int itemtype item type (optional)
 * @param  array itemids array of item ids to get
 * @since 25 Feb 2006
 * @return array Array containing the itemlink(s) for the item(s).
 */
function julian_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('ViewJulian', 0)) {
        return $itemlinks;
    }

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('julian', 'user', 'get',
            array('event_id' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('julian', 'user', 'viewevent',
                array('event_id' => $itemid)),
            'title' => xarML('Display Event'),
            'label' => xarVarPrepForDisplay($item['summary']));
    }
    return $itemlinks;
}
?>