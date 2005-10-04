<?php
/**
 * Utility function to retrieve the list of item types of this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team 
 */
/**
 * utility function to retrieve the list of item types of this module (if any)
 *
 * @returns array
 * @return array containing the item types and their description
 */
function courses_userapi_getitemtypes($args)
{
    $itemtypes = array();

    // Let's see if this is usefull
    $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('Courses')),
                          'title' => xarVarPrepForDisplay(xarML('View All Courses')),
                          'url'   => xarModURL('courses','user','view'));
    
    $itemtypes[2] = array('label' => xarVarPrepForDisplay(xarML('Planned Courses')),
                          'title' => xarVarPrepForDisplay(xarML('View All Planned Courses')),
                          'url'   => xarModURL('courses','user','view'));


    return $itemtypes;
}

?>
