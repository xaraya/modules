<?php
/**
 * Utility function to pass individual item links 
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V. 
 */
/**
 * utility function to pass individual item links to whoever
 * 
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function sigmapersonnel_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('ViewSIGMAPersonnel', 0)) {
        return $itemlinks;
    } 

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('sigmapersonnel', 'user', 'get',
            array('personid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('sigmapersonnel', 'user', 'display',
                array('personid' => $itemid)),
            'title' => xarML('Display a person Item'),
            'label' => xarVarPrepForDisplay($item['lastname']));
    } 
    return $itemlinks;
} 
?>