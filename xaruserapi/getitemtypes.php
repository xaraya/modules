<?php
/**
 * Utility function to retrieve the list of item types of this module
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @return array containing the item types and their description
 */
function maxercalls_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Let's see if this is usefull
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Maxercalls')),
                          'title' => xarVarPrepForDisplay(xarML('View All Calls')),
                          'url'   => xarModURL('maxercalls','user','view'));

    $itemtypes[2] = array('label' => xarVarPrepForDisplay(xarML('Maxers')),
                          'title' => xarVarPrepForDisplay(xarML('View All Maxers')),
                          'url'   => xarModURL('maxercalls','user','viewmaxers'));




    return $itemtypes;
}
?>