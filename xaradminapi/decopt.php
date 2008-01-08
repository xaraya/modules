<?php
/*
 *
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * decrement poll option position
 * @param $args['pid'] the ID of the poll to decrement
 * @param $args['optnum'] the number of the option to decrement
 * @returns bool
 * @return true on success, false on failure
 */
function polls_adminapi_decopt($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid)) || (!isset($opt))) {
        throw new BadParameterException(array($pid,$option),'Missing Poll id (#(1)), or Options (#(2))');
    }

    // Get poll information
    $poll = xarModAPIFunc('polls',
                           'user',
                           'get',
                           array('pid' => $pid));

    if (empty($poll)) {
                throw new IDNotFoundException($pid,'Unable to found poll id (#(1))');
            }

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[pid]:$poll[type]")) {
        return;
    }


    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];
    $pollsinfocolumn = &$xartable['polls_info_column'];

    // Swap positions - three updates
    $sql = "UPDATE $pollsinfotable
            SET optnum = optnum + 900
            WHERE pid = ?
            AND optnum = ?";
    $result = $dbconn->Execute($sql, array((int)$pid, $opt));
    if(!$result){
        return;
    }
    $optplusone=$opt + 1;
    $sql = "UPDATE $pollsinfotable
            SET optnum = ?
            WHERE pid = ?
            AND optnum = ?";
    $result = $dbconn->Execute($sql, array($opt, (int)$pid, $optplusone));
    if(!$result){
        return;
    }
    $optplus=$opt + 900;
    $sql = "UPDATE $pollsinfotable
            SET optnum = optnum - 899
            WHERE pid = ?
            AND optnum =?";
    $result = $dbconn->Execute($sql, array((int)$pid, $optplus));
    if(!$result){
        return;
    }

    return true;
}

?>
