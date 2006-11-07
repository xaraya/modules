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
        $msg = xarML('Missing poll ID, title, or type');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    if($private != 1){
        $private = 0;
    }

    // Get poll information
    $poll = xarModAPIFunc('polls', 'user', 'get', array('pid' => $pid));

    // Security check
    if (!xarSecurityCheck('EditPolls',1,'All',"$poll[title]:All:$pid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
   // $prefix = xarConfigGetVar('prefix');

    $sql = "UPDATE $pollstable
            SET xar_title = ?,
            xar_type = ?,
            xar_private = ?,
            xar_start_date = ?,
            xar_end_date = ?
            WHERE xar_pid = ?";

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
