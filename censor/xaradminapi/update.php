<?php
/**
 * update an censored word
 * 
 * @param  $args ['cid'] the ID of the link
 * @param  $args ['keyword'] the new keyword of the link
 */
function censor_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);

    if (!isset($comment)) {
        $comment = '';
    } 
    // Argument check
    if ((!isset($cid)) ||
            (!isset($keyword))) {
        $msg = xarML('Invalid Parameter Count',
            join(', ', $invalid), 'admin', 'update', 'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // The user API function is called
    $link = xarModAPIFunc('censor',
        'user',
        'get',
        array('cid' => $cid));

    if ($link == false) {
        $msg = xarML('No Such Link Present', 'censor');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    } 
    // Security Check
    if (!xarSecurityCheck('EditCensor')) return; 
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $censortable = $xartable['censor']; 
    // Update the link
    $query = "UPDATE $censortable
            SET xar_keyword = '" . xarVarPrepForStore($keyword) . "'
            WHERE xar_cid = " . xarVarPrepForStore($cid);
    $result = &$dbconn->Execute($query);
    if (!$result) return; 
    // Let the calling process know that we have finished successfully
    return true;
}


?>