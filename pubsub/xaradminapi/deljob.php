<?php

/**
 * delete a pubsub job from the queue
 * @param $args['handlingid'] ID of the job to delete
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deljob($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($handlingid) || !is_numeric($handlingid)) {
        $invalid[] = 'handlingid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) function #(3)() in module #(4)',
                    join(', ',$invalid), 'deljob', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('DeletePubSub', 1, 'item', "All:All:$handlingid:All")) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Delete item
    $query = "DELETE FROM $pubsubprocesstable
              WHERE xar_handlingid = " . xarVarPrepForStore($handlingid);
    $result = $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>
