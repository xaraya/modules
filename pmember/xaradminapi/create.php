<?php
/**
 * create a new headline
 * @param $args['uid'] uid that we are logging
 * @param $args['time'] time the item was created
 * @param $args['expires'] time the item will expire
 * @returns int
 * @return headline ID on success, false on failure
 */
function pmember_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($uid)) {
        $msg = xarML('Invalid Parameter Count', 'admin', 'create', 'pmember');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['pmember'];
    // Need to see if the uid exists, and just update it if so
    // If not create a new entry.
    $check = xarModAPIFunc('pmember',
                           'user',
                           'get',
                           array('uid' => $uid));

    if ($uid != $check['uid']){
        // Add item
        $query = "INSERT INTO $table (
                  xar_uid,
                  xar_subscribed,
                  xar_expires)
                VALUES (
                  '" . xarVarPrepForStore($uid) . "',
                  '" . xarVarPrepForStore($time) . "',
                  '" . xarVarPrepForStore($expire) . "')";
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    } else {
        // Update it instead
        $query = "UPDATE $table
                SET xar_subscribed = '" . xarVarPrepForStore($time) . "',
                    xar_expires = '" . xarVarPrepForStore($expire) . "'
                WHERE xar_uid = " . xarVarPrepForStore($uid);
        $result =& $dbconn->Execute($query);
        if (!$result) return;
    }
    // Return the id of the newly created link to the calling process
    return true;
}
?>