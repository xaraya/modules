<?php

/**
 * delete a template item
 * @param $args['tid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 */
function ratings_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

/*
    // Argument check
    if (!isset($tid)) {
        $msg = xarML('Bad Parameter in API',
                    join(', ',$invalid), 'admin', 'create', 'ratings');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

// Security Check
	if(!xarSecurityCheck('DeleteRatings')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();
    $templatetable = $xartable['template'];

    // Delete item
    $query = "DELETE FROM $templatetable
            WHERE xar_tid = " . xarVarPrepForStore($tid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
*/
    return true;
}

?>