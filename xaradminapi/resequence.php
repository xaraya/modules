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
 * resequence a poll's options
 */
function polls_adminapi_resequence($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($pid)) {
        $msg = xarML('Missing poll ID');
        xarErrorSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];

    // Get the information
    $sql = "SELECT xar_optnum
            FROM $pollsinfotable
            WHERE xar_pid = ?
            ORDER BY xar_optnum";
    $result = $dbconn->Execute($sql, array((int)$pid));

    // Fix sequence numbers
    $seq=1;
    while(list($optnum) = $result->fields) {
        $result->MoveNext();

        if ($optnum != $seq) {
            $query = "UPDATE $pollsinfotable
                SET xar_optnum= ?
                WHERE xar_pid= ?
                AND xar_optnum= ?";
            $result1 = $dbconn->Execute($query, array($seq, (int)$pid, $optnum));
            if(!$result1){
                return;
            }

        }
        $seq++;
    }
    $result->Close();

    return;
}

?>
