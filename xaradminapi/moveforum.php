<?php

/**
 * Move a forum - simple move in a flat list
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author jojodee
*/
/**
 * @description move a forum - the easy way out for now
 * Let's leave more complex moves until we decide to do sub forums or not
 * @author jojodee
 * @param $cfid the forum id
 * @param $swaporderid the $fid of the reference forum
 * @param $swapposition the new position
 */

function xarbb_adminapi_moveforum($args)
{
    extract($args);

    //get the array of all forums in correct order ...
    $forums = xarModAPIFunc('xarbb', 'user', 'getallforums'); //these must be in forder ...

    if (empty($forums) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $xbbforumstable = $xartable['xbbforums'];

    // get the info on the current forum
    $currentforum = xarModAPIFunc('xarbb', 'user', 'getforum', array('fid' => $cfid));

    $currentorderid=$currentforum['forder'];
    $currentforumid=$currentforum['fid'];

    // don't assume the value of the order field is contiguous number sequence.... some forums are deleted
    foreach ($forums as $id=>$forum) {
        // some fiddling - array starts with zero ..
        if (($forums[$id]['forder'] == $currentorderid) && strtolower($moveaction) == 'up') {
            // We need to find the position fid before (less)
            $swapforumid = (int)$forums[$id-1]['fid'];
            $swaporderid = $forums[$id-1]['forder'];
            $swapposition = $id;
            $currentposition= $id+1;
        } elseif (($forums[$id]['forder'] == $currentorderid) && strtolower($moveaction) == 'down') {
            // We need to find the position fid after (more)
            $swapforumid = (int)$forums[$id+1]['fid'];
            $swaporderid = $forums[$id+1]['forder'];
            $swapposition = $id+2;
            $currentposition= $id+1;
        }
    }


    // Update the current forum
    $query = 'UPDATE ' . $xbbforumstable
        . ' SET xar_forder = ?'
        . ' WHERE xar_fid = ?';

    $result = $dbconn->execute($query, array($swaporderid, $cfid));
    if (!$result) return;

    // Update the reference  forum
    $query = 'UPDATE ' . $xbbforumstable
        . ' SET xar_forder = ?'
        . ' WHERE xar_fid = ?';

    $result = $dbconn->execute($query, array($currentorderid, $swapforumid));
    if (!$result) return;

    $result->close();

    return true;
}

?>