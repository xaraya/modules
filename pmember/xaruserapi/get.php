<?php
/**
 * get a specific headline
 * @poaram $args['uid']uid of the user
 * @returns array
 * @return link array, or false on failure
 */
function pmember_userapi_get($args)
{
    extract($args);
    if (empty($uid) || !is_numeric($uid)) {
        $msg = xarML('Invalid User');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pmembertable = $xartable['pmember'];

    // Get headline
    $query = "SELECT xar_uid,
                     xar_subscribed,
                     xar_expires
            FROM $pmembertable
            WHERE xar_uid = " . xarVarPrepForStore($uid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($uid, $subscribed, $expires) = $result->fields;
    $result->Close();

    $link = array('uid'         => $uid,
                  'subscribed'  => $subscribed,
                  'expires'     => $expires);
    return $link;
}
?>