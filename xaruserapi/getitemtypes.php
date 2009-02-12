<?php
/**
 * Utility function to retrieve the list of item types
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabContest Module
 * @link http://xaraya.com/index.php/release/892.html
 * @author LabContest Module Development Team
 */
/**
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @author the LabContest module development team
 * @return array containing the item types and their description
 */
function labaffiliate_userapi_getitemtypes($args)
{
    $itemtypes = array();

   /*  do not use this if you only handle one type of items in your module */
   
       $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Programs')),
                          'title' => xarVarPrepForDisplay(xarML('View Programs')),
                          'url'   => xarModURL('labaffiliate','user','view'));

       $itemtypes[2] = array('label' => xarVarPrepForDisplay(xarML('Affiliates')),
                          'title' => xarVarPrepForDisplay(xarML('View Affiliates')),
                          'url'   => xarModURL('labaffiliate','affiliate','view'));
   
       $itemtypes[3] = array('label' => xarVarPrepForDisplay(xarML('Membership')),
                          'title' => xarVarPrepForDisplay(xarML('View Membership')),
                          'url'   => xarModURL('labaffiliate','membership','view'));

    return $itemtypes;
}
?>
