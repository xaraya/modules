<?php

/**
 * get a specific poll hooked to some external module item
 * @param $args['modname'] module name of the original item
 * @param $args['itemtype'] item type of the original item
 * @param $args['objectid'] object id of the original item
 * @returns array
 * @return item array, or false on failure
 */
function polls_userapi_gethooked($args)
{
    // Get arguments from argument array
    extract($args);

    if (empty($modname)) {
        $modname = xarModGetName();
    }
    $modid = xarModGetIDFromName($modname);
    if (empty($modid)) {
        return;
    }
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($objectid)) {
        $objectid = 0;
    }

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

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
            WHERE  ".$prefix."_modid = " . xarVarPrepForStore($modid) . "
               AND ".$prefix."_itemtype = " . xarVarPrepForStore($itemtype) . "
               AND ".$prefix."_itemid = " . xarVarPrepForStore($objectid);

    $result =& $dbconn->SelectLimit($sql, 1);

    // Error check
    if (!$result) {
        return;
    }

    // Check for no rows found, and if so return
    if ($result->EOF) {
        return;
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
        return;
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
