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
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();
    $pingtable = $xartable['ping'];
    // Update the link
    $query = "UPDATE $pingtable
            SET xar_url     = ?,
                xar_method  = ?
            WHERE xar_id    = ?";
    $result =& $dbconn->Execute($query, array($url, $method, (int)$id));
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    xarModCallHooks('item', 'update', $id, '');
    return true;
}
?>
