<?php
/**
 * Polls Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 */

/**
 * update a poll
 * @param $args['pid'] ID of poll
 * @param $args['title'] ID of poll
 */
function polls_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($pid)) || (!isset($title)) || (!isset($type))) {
        throw new BadParameterException(array($pid,$option, $type),'Missing Poll id (#(1)), or Poll title (#(2)) or Poll type (#(3))');
    }

    if($private != 1){
        $private = 0;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[pid]:$poll[type]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
   // $prefix = xarConfigVars::get('prefix');
   $prefix = xarConfigVars::get(null, 'prefix');

    $sql = "UPDATE $pollstable
            SET title = ?,
            type = ?,
            private = ?,
            start_date = ?,
            end_date = ?
            WHERE pid = ?";

    $bindvars = array($title, $type, $private, $start_date, $end_date, (int)$pid);
    $result = $dbconn->Execute($sql, $bindvars);

    if (!$result) {
        return;
    }
    $args['pid'] = $pid;
    $args['module'] = 'polls';
    $args['itemtype'] = 0;
    $args['itemid'] = $pid;

    xarModCallHooks('item', 'update', $pid, $args);

    return true;
}

?>
