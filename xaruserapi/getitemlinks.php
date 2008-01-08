<?php
/**
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
 * utility function to pass individual item links to whoever
 *
 * @param $args['itemtype'] item type (not relevant here)
 * @param $args['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function polls_userapi_getitemlinks($args)
{
    extract($args);

    $itemlinks = array();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];

    // Get polls
    $sql = "SELECT pid,
                   title,
                   type
            FROM $pollstable
            WHERE pid IN (". join(', ', $itemids) . ")";
    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    // Put polls into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $title,$type) = $result->fields;
        if (xarSecurityCheck('ViewPolls',0,'Polls',"$pid:$type")) {
             $itemlinks[$pid] = array('url'   => xarModURL('polls', 'user', 'results',
                                                           array('pid' => $pid)),
                                      'title' => xarML('View Poll'),
                                      'label' => xarVarPrepForDisplay($title));
        }
    }
    $result->Close();

    return $itemlinks;
}

?>
