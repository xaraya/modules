<?php

/**
 * get a specific item
 * @param $args['pid'] id of poll to get (optional)
 * @returns array
 * @return item array, or false on failure
 */
function polls_userapi_get($args)
{
    // Get arguments from argument array
    extract($args);

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

    // Selection check
    if (!empty($pid)) {
        $extra = "WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid);
    } else {
        $extra = "WHERE ".$prefix."_modid = " . xarModGetIDFromName('polls');
        $extra .= " ORDER BY ".$prefix."_pid DESC";
    }

    // Get item
    $sql = "SELECT ".$prefix."_pid,
                   ".$prefix."_title,
                   ".$prefix."_type,
                   ".$prefix."_open,
                   ".$prefix."_private,
                   ".$prefix."_modid,
                   ".$prefix."_itemtype,
                   ".$prefix."_itemid,
                   ".$prefix."_opts,
                   ".$prefix."_votes,
                   ".$prefix."_reset
            FROM $pollstable
            $extra";

    $result = $dbconn->SelectLimit($sql, 1);

    // Error check
    if (!$result) {
        return false;
    }

    // Check for no rows found, and if so return
    if ($result->EOF) {
        return false;
    }

    // Obtain the poll information from the result set
    list($pid, $title, $type, $open, $private, $modid, $itemtype, $itemid, $opts, $votes, $reset) = $result->fields;

    $result->Close();

    // Security check
	if(!xarSecurityCheck('ViewPolls',0,'All',"$title:All:$pid")){
		return;
	}

    // Get the options for this poll
    $pollsinfotable = $xartable['polls_info'];

    $sql = "SELECT ".$prefix."_optnum,
                   ".$prefix."_optname,
                   ".$prefix."_votes
            FROM $pollsinfotable
            WHERE ".$prefix."_pid = " . xarVarPrepForStore($pid) . "
            ORDER BY ".$prefix."_optnum";
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return false;
    }

    $options = array();
    for(; !$result->EOF; $result->MoveNext()) {
        list($optnum, $optname, $optvotes) = $result->fields;
        $options[$optnum] = array('name' => $optname,
                                  'votes' => $optvotes);
    }
    $result->Close();

    // Create the item array
    $item = array('pid' => $pid,
                  'title' => $title,
                  'type' => $type,
                  'open' => $open,
                  'private' => $private,
                  'modid' => $modid,
                  'itemtype' => $itemtype,
                  'itemid' => $itemid,
                  'opts' => $opts,
                  'votes' => $votes,
                  'reset' => $reset,
                  'options' => $options);

    // Return the item array
    return $item;
}

?>
