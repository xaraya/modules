<?php

/**
 * update an existing pubsub job
 * @param $args['handlingid'] the ID of this job
 * @param $args['pubsubid'] the new pubsub id for this job
 * @param $args['objectid'] the new object id for this job
 * @param $args['templateid'] the template id for this job
 * @param $args['status']   the new status for this job
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_updatejob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($handlingid) || !is_numeric($handlingid)) {
        $invalid[] = 'handlingid';
    }
    if (!isset($pubsubid) || !is_numeric($pubsubid)) {
        $invalid[] = 'pubsubid';
    }
    if (!isset($objectid) || !is_numeric($objectid)) {
        $invalid[] = 'objectid';
    }
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (!isset($status) || !is_string($status)) {
        $invalid[] = 'status';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'updatejob', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('EditPubSub', 1, 'item', "All:All:$handlingid:All")) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Update the item
    $query = "UPDATE $pubsubprocesstable
              SET xar_pubsubid = " . xarVarPrepForStore($pubsubid) . ",
                  xar_objectid = " . xarVarPrepForStore($objectid) . ",
                  xar_status = '" . xarVarPrepForStore($status) . "'
            WHERE xar_handlingid = " . xarVarPrepForStore($handlingid);
    $result = $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>
