<?php

/**
 * create a poll
 * @param $args['title'] title of poll
 * @param $args['polltype'] type of poll ('single' for select one
 *                                      'multi' for select many)
 * @param $args['time'] time when the poll was created (import only)
 * @param $args['votes'] number of votes for this poll (import only)
 * @param $args['module'] module of the item this poll relates to (hooks only)
 * @param $args['itemtype'] itemtype of the item this poll relates to (hooks only)
 * @param $args['itemid'] itemid of the item this poll relates to (hooks only)
 * @returns int
 * @return ID of poll, false on failure
 */
function polls_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($title)) || (!isset($polltype))) {
        $msg = xarML('Missing poll title or type');
        xarExceptionSet(XAR_USER_EXCEPTION,
                    'BAD_DATA',
                     new DefaultUserException($msg));
        return;
    }
    if ($private != 1){
        $private = 0;
    }
    // Security check
    if (!xarSecurityCheck('AddPolls')) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pollstable = $xartable['polls'];
    $prefix = xarConfigGetVar('prefix');

    $nextId = $dbconn->GenId($pollstable);

    if (empty($time)) {
        $time = time();
    }
    if (empty($votes)) {
        $votes = 0;
    }
    if (empty($module)) {
        $module = 'polls';
    }
    $modid = xarModGetIDFromName($module);
    if (empty($itemtype)) {
        $itemtype = 0;
    }
    if (empty($itemid)) {
        $itemid = 0;
    }
    $sql = "INSERT INTO $pollstable (
              ".$prefix."_pid,
              ".$prefix."_title,
              ".$prefix."_type,
              ".$prefix."_open,
              ".$prefix."_private,
              ".$prefix."_votes,
              ".$prefix."_modid,
              ".$prefix."_itemtype,
              ".$prefix."_itemid,
              ".$prefix."_reset)
            VALUES (
              " . xarVarPrepForStore($nextId) . ",
              '" . xarVarPrepForStore($title) . "',
              '" . xarVarPrepForStore($polltype) . "',
              1,
              '" . xarVarPrepForStore($private) . "',
              '" . xarVarPrepForStore($votes) . "',
              '" . xarVarPrepForStore($modid) . "',
              '" . xarVarPrepForStore($itemtype) . "',
              '" . xarVarPrepForStore($itemid) . "',
              ".xarVarPrepForStore($time).")";

    $result = $dbconn->Execute($sql);


    if (!$result) {
        return;
    }
    $pid = $dbconn->PO_Insert_ID($pollstable, 'xar_pid');
    return $pid;
}

?>
