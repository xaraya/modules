<?php

/**
 * delete a forum
 * @param $args['fid'] ID of the forum
 * @returns bool
 * @return true on success, false on failure
 */
function xarbb_adminapi_deletetopics($args)
{

    // BIG FIXME HERE
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($fid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'xarbb');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('DeletexarBB')) return;

    // Get datbase setup
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $xbbtopicstable = $xartable['xbbtopics'];

    // Delete the item
    $query = "DELETE FROM $xbbtopicstable
              WHERE xar_fid = " . xarVarPrepForStore($fid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
// should call item delete hooks for each topic, really
//    xarModCallHooks('item', 'delete', $fid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>
