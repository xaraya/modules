<?php
/**
 * delete an censored word
 * 
 * @param  $args ['cid'] ID of the link
 * @returns bool
 * @return true on success, false on failure
 */
function censor_adminapi_delete($args)
{ 
    // Get arguments from argument array
    extract($args); 
    // Argument check
    if (!isset($cid)) {
        $msg = xarML('Invalid Parameter Count',
            join(', ', $invalid), 'admin', 'delete', 'censor');
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
        $msg = xarML('No Such Word Present', 'censor');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    } 
    // Security Check
    if (!xarSecurityCheck('DeleteCensor')) return; 
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $censortable = $xartable['censor']; 
    // Delete the item
    $query = "DELETE FROM $censortable
            WHERE xar_cid = " . xarVarPrepForStore($cid);
    $result = &$dbconn->Execute($query);
    if (!$result) return; 
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $cid, ''); 
    // Let the calling process know that we have finished successfully
    return true;
}

?>