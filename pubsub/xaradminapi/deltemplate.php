<?php

/**
 * delete a pubsub template
 * @param $args['templateid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function pubsub_adminapi_deltemplate($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($templateid) || !is_numeric($templateid)) {
        $invalid[] = 'templateid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for function #(2)() in module #(3)',
                    join(', ',$invalid), 'deltemplate', 'Pubsub');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security check
    if (!xarSecurityCheck('DeletePubSub')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubtemplatestable = $xartable['pubsub_templates'];

    // Delete item
    $query = "DELETE FROM $pubsubtemplatestable
              WHERE xar_templateid = " . xarVarPrepForStore($templateid);
    $result = $dbconn->Execute($query);
    if (!$result) return;

    return true;
}

?>
