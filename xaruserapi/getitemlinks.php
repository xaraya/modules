<?php
/**
 * Utility function to pass individual item links
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
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
    extract($args);
    // Create array
    $itemlinks = array();
    // Security check
    if (!xarSecurityCheck('ViewSIGMAPersonnel', 0)) {
        return $itemlinks;
    }

    switch ($itemtype) {
        case '1':
            foreach ($args['itemids'] as $itemid) {
                $item = xarModAPIFunc('sigmapersonnel', 'user', 'get',
                    array('personid' => $itemid));
                if (!isset($item)) return;
                $itemlinks[$itemid] = array('url' => xarModURL('sigmapersonnel', 'user', 'display',
                        array('personid' => $itemid)),
                    'title' => xarML('Display a person Item'),
                    'label' => xarVarPrepForDisplay($item['lastname']));
            }
        case '2':
            // don't know yet
            $itemlinks = array();
    }
    return $itemlinks;
}
?>