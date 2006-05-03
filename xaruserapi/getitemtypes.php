<?php
/**
 * Utility function to retrieve the list of item types
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @author the Example module development team
 * @return array containing the item types and their description
 */
function example_userapi_getitemtypes($args)
{
    $itemtypes = array();

   /*  do not use this if you only handle one type of items in your module */
   
   /*    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Example Items')),
                          'title' => xarVarPrepForDisplay(xarML('View Example Items')),
                          'url'   => xarModURL('example','user','view'));
    ...
   */

    return $itemtypes;
}
?>