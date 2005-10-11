<?php
/**
 * Retreive item types
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * Utility function to retrieve the list of item types of this module.
 *
 * @returns array
 * @return array containing the item types and their description
 */
function lists_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Get list types.
    $listtypes = xarModAPIFunc('lists', 'user', 'getlisttypes');

    foreach ($listtypes as $listtype) {
        $tid = $listtype['tid'];
        $itemtypes[$tid] = array(
            'label' => xarVarPrepForDisplay($listtype['type_name']),
            'title' => xarVarPrepForDisplay(xarML('View #(1)', $listtype['type_name'])),
            'url'   => xarModURL('lists', 'admin', 'view', array('tid' => $tid))
        );
    }

    return $itemtypes;
}

?>
