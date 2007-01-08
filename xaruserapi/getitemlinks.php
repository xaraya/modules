<?php
/**
 * Utility function to pass individual item links to whoever
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
 /**
 * Utility function to pass individual item links to whoever
 *
 * @author the ITSP module development team
 * @param int $args ['itemtype'] item type (optional)
 * @param array $args ['itemids'] array of item ids to get
 * @return array containing the itemlink(s) for the item(s).
 */
function itsp_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('ViewITSP', 0)) {
        return $itemlinks;
    }
    extract($args);

    foreach ($itemids as $itemid) {
        if (!isset($itemtype) || $itemtype == 99999) {
            $item = xarModAPIFunc('itsp', 'user', 'get',
                array('itspid' => $itemid));
            $itemlinks[$itemid] = array('url' => xarModURL('itsp', 'user', 'itsp',
                    array('itspid' => $itemid)),
                'title' => xarML('Display ITSP'),
                'label' => xarML('User ITSP'));
        } elseif ($itemtype == 99998) {
            $item = xarModAPIFunc('itsp', 'user', 'get_plan',
                array('planid' => $itemid));
            $itemlinks[$itemid] = array('url' => xarModURL('itsp', 'user', 'display',
                    array('planid' => $itemid)),
                'title' => xarML('Display ITSP Item'),
                'label' => xarVarPrepForDisplay($item['name']));
        } else {
            $item = xarModAPIFunc('itsp', 'user', 'get_planitem',
                array('pitemid' => $itemid));

            if (!isset($item)) return;
            $itemlinks[$itemid] = array('url' => xarModURL('itsp', 'admin', 'modify_pitem',
                    array('pitemid' => $itemid)),
                'title' => xarML('Modify Plan Item'),
                'label' => xarVarPrepForDisplay($item['pitemname']));
        }
    }
    return $itemlinks;
}
?>