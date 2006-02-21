<?php
/**
 * Utility function to retrieve the list of item types
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
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
 * @author MichelV <michelv@xarayahosting.nl>
 * @return array containing the item types and their description
 * @todo MichelV <1> decide on the setup in here
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
    $itemtypes[4] = array('label' => xarVarPrepForDisplay(xarML('Linked Courses')),
                      'title' => xarVarPrepForDisplay(xarML('Linked courses')),
                      'url'   => xarModURL('itsp','user','view'));
    $itemtypes[5] = array('label' => xarVarPrepForDisplay(xarML('External courses')),
                      'title' => xarVarPrepForDisplay(xarML('All externally linked courses')),
                      'url'   => xarModURL('itsp','admin','external'));
    return $itemtypes;
}
?>