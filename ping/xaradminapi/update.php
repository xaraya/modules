<?php
/**
 * update an item
 * @param $args['id'] the ID of the item
 * @param $args['url'] the new url of the item
 */
function ping_adminapi_update($args)
{   // Get arguments from argument array
    extract($args);
    // Argument check
    if ((!isset($id)) ||
        (!isset($url))) {
        $msg = xarML('Invalid Parameter Count');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // The user API function is called
    $link = xarModAPIFunc('ping',
                          'user',
                          'get',
                          array('id' => $id));
    if ($link == false) return;
    // Security Check
	if(!xarSecurityCheck('Adminping')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pingtable = $xartable['ping'];
    // Update the link
    $query = "UPDATE $pingtable
            SET xar_url     = '" . xarVarPrepForStore($url) . "',
                xar_method  = '" . xarVarPrepForStore($method) . "'
            WHERE xar_id    = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    xarModCallHooks('item', 'update', $id, '');
    return true;
}
?>