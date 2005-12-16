<?php
/**
 * Utility function to retrieve the list of item types
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @author the ITSP module development team
 * @return array containing the item types and their description
 */
function itsp_userapi_getitemtypes($args)
{
    $itemtypes = array();

   /*  do not use this if you only handle one type of items in your module */

   /*    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('ITSP Items')),
                          'title' => xarVarPrepForDisplay(xarML('View ITSP Items')),
                          'url'   => xarModURL('itsp','user','view'));
    ...
   */

    return $itemtypes;
}
?>