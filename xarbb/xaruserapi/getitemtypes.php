<?php
/**
 * File: $Id$
 * 
 * Standard utility function to retrieve list of items types for this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function xarbb_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Get publication types

    $itemtypes[1] = array('label' => "Forum",
    				      'title' => "Display Forum",
                          'url' => xarModURL('xarbb','user','main',array()));

    $itemtypes[2] = array('label' => "Forum Topics",
    				      'title' => "Display Forum Topics",
                          'url' => xarModURL('xarbb','user','main',array()));
    return $itemtypes;
}

?>
