<?php
/**
 * Utility function to pass individual item links to whoever
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
 /**
 * Utility function to pass individual item links to whoever
 * 
 * @author jojodee
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function legis_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('ViewLegis', 0)) {
        return $itemlinks;
    } 

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('legis', 'user', 'get',
            array('cdid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('legis', 'user', 'display',
                array('cdid' => $itemid)),
                      'title' => xarML('Display Legislation'),
                      'label' => xarVarPrepForDisplay($item['cdtitle']));
    } 
    return $itemlinks;
} 
?>
