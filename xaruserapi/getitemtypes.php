<?php
/**
 * Utility function to retrieve the list of item types
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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
 * @todo decide on the setup in here
 */
function itsp_userapi_getitemtypes($args)
{
    $itemtypes = array();

    /*  do not use this if you only handle one type of items in your module */
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('ITSP Plans')),
                      'title' => xarVarPrepForDisplay(xarML('View ITSP Plans')),
                      'url'   => xarModURL('itsp','user','view'));

    $itemtypes[2] = array('label' => xarVarPrepForDisplay(xarML('ITSPs')),
                      'title' => xarVarPrepForDisplay(xarML('View ITSPs')),
                      'url'   => xarModURL('itsp','user','viewitsp'));

    $itemtypes[3] = array('label' => xarVarPrepForDisplay(xarML('ITSP Planitems')),
                      'title' => xarVarPrepForDisplay(xarML('View ITSP Planitems')),
                      'url'   => xarModURL('itsp','user','view'));
    return $itemtypes;
}
?>