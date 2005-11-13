<?php
/**
 * Utility function to retrieve the list of item types of this module
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
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function sigmapersonnel_userapi_getitemtypes($args)
{
    $itemtypes = array();

/*  // do not use this if you only handle one type of items in your module
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Example Items')),
                          'title' => xarVarPrepForDisplay(xarML('View Example Items')),
                          'url'   => xarModURL('sigmapersonnel','user','view'));
    ...
*/

    return $itemtypes;
}

?>
