<?php
/**
 * File: $Id:
 * 
 * Utility function to retrieve the list of item types of this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team 
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function example_userapi_getitemtypes($args)
{
    $itemtypes = array();

/*  // do not use this if you only handle one type of items in your module
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Example Items')),
                          'title' => xarVarPrepForDisplay(xarML('View Example Items')),
                          'url'   => xarModURL('example','user','view'));
    ...
*/

    return $itemtypes;
}

?>
