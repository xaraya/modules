<?php
/**
 * Utility function to retrieve the list of item types of this module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V.
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function sigmapersonnel_userapi_getitemtypes($args)
{
    $itemtypes = array();

    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Personnel Items')),
                          'title' => xarVarPrepForDisplay(xarML('View Persons')),
                          'url'   => xarModURL('sigmapersonnel','user','view'));
    $itemtypes[2] = array('label' => xarVarPrepForDisplay(xarML('Presence Items')),
                          'title' => xarVarPrepForDisplay(xarML('View Presences')),
                          'url'   => xarModURL('sigmapersonnel','user','view'));

    return $itemtypes;
}

?>
