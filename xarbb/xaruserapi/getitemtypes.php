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

   //<jojodee> Took this out while debuggin the hooks probs
   //Do we need this?
 
    $itemtypes[0] = array('label' => xarML('Main Forum Configuration'),
    				      'title' => xarML('Main Forum Configuration'),
                          'url' => xarModURL('xarbb','user','main',array()));

    $forums = xarModAPIFunc('xarbb',
                            'user',
                            'getallforums');

    foreach($forums as $forum){
        $itemtypevalue = $forum['fid'];
        $itemtypes[$itemtypevalue] = array('label' => $forum['fname'] . ' ' . xarML('Forum'),
                                           'title' => xarML('Individual Forum Configuration'),
                                           'url' => xarModURL('xarbb','user','main',array('fid' => $forum['fid'])));
    }

    return $itemtypes;
}

?>
