<?php

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
    $prefix = xarConfigGetVar('prefix');

    // Get polls
    $sql = "SELECT ".$prefix."_pid,
                   ".$prefix."_title
            FROM $pollstable
            WHERE ".$prefix."_pid IN (". join(', ', $itemids) . ")";
    $result =& $dbconn->Execute($sql);
    if (!$result) return;

    // Put polls into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $title) = $result->fields;
        if (xarSecurityCheck('ViewPolls',0,'All',"$title:All:$pid")) {
             $itemlinks[$pid] = array('url'   => xarModURL('polls', 'user', 'display',
                                                           array('pid' => $pid)),
                                      'title' => xarML('View Poll'),
                                      'label' => xarVarPrepForDisplay($title));
        }
    }
    $result->Close();

    return $itemlinks;
}

?>
