<?php
/**
 * delete a subscription
 * @param $args['uid'] user to delete
 * @returns bool
 * @return true on success, false on failure
 */
function pmember_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($uid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('pmember',
                          'user',
                          'get',
                          array('uid' => $uid));

    if ($link == false) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['pmember'];

    // Delete the item
    $query = "DELETE FROM $table
            WHERE xar_uid = ?";
    $bindvars = array($uid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $uid, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>