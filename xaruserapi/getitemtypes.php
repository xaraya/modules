<?php
/**
* Get item types for hitcount, etc.
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function ebulletin_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // publications
    $itemtypes[0] = array('label' => xarVarPrepForDisplay(xarML('Publications')),
                          'title' => xarVarPrepForDisplay(xarML('View Publications')),
                          'url'   => xarModURL('audio', 'user', 'view'));

    // issues
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Issues')),
                          'title' => xarVarPrepForDisplay(xarML('View Archive')),
                          'url'   => xarModURL('audio', 'user', 'view'));

    return $itemtypes;
}

?>
