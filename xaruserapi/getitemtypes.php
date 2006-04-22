<?php
/**
 * Standard utility function to retrieve list of items types for this module
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
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

    $forums = xarModAPIFunc('xarbb', 'user', 'getallforums');

    foreach($forums as $forum) {
        $itemtypevalue = $forum['fid'];
        $itemtypes[$itemtypevalue] = array(
            'label' => xarML('#(1) Forum', $forum['fname']),
            'title' => xarML('Individual Forum Configuration'),
            'url' => xarModURL('xarbb', 'user', 'viewforum', array('fid' => $forum['fid']))
        );
    }

    return $itemtypes;
}

?>