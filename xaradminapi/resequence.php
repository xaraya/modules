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
              throw new IDNotFoundException($pid,'Unable to find poll id (#(1))');
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollsinfotable = $xartable['polls_info'];

    // Get the information
    $sql = "SELECT optnum
            FROM $pollsinfotable
            WHERE pid = ?
            ORDER BY optnum";
    $result = $dbconn->Execute($sql, array((int)$pid));

    // Fix sequence numbers
    $seq=1;
    while(list($optnum) = $result->fields) {
        $result->MoveNext();

        if ($optnum != $seq) {
            $query = "UPDATE $pollsinfotable
                SET optnum= ?
                WHERE pid= ?
                AND optnum= ?";
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
