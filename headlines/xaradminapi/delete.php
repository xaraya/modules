<?php
/**
 * delete an headlines
 * @param $args['hid'] ID of the headline
 * @returns bool
 * @return true on success, false on failure
 */
function headlines_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($hid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'delete', 'Autolinks');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('headlines',
                          'user',
                          'get',
                          array('hid' => $hid));

    if ($link == false) return;

    // Security Check
	if(!xarSecurityCheck('DeleteHeadlines')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $headlinestable = $xartable['headlines'];

    // Delete the item
    $query = "DELETE FROM $headlinestable
            WHERE xar_hid = " . xarVarPrepForStore($hid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $hid, '');

    // Let the calling process know that we have finished successfully
    return true;
}
?>
