<?php
/**
 * create a new censored word
 * 
 * @param  $args ['keyword'] keyword of the item
 * @returns int
 * @return censor ID on success, false on failure
 */
function censor_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($keyword)) {
        $msg = xarML('Invalid Parameter Count',
            join(', ', $invalid), 'admin', 'create', 'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 
    // Security Check
    if (!xarSecurityCheck('AddCensor')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $censortable = $xartable['censor'];
    // Get next ID in table
    $nextId = $dbconn->GenId($censortable); 
    // Add item
    $query = "INSERT INTO $censortable (
              xar_cid,
              xar_keyword,
              xar_case_sensitive,
              xar_match_case,
              xar_locale)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($keyword) . "',
              '" . xarVarPrepForStore($case) . "',
              '" . xarVarPrepForStore($matchcase) . "',
              '" . xarVarPrepForStore($locale) . "')";
    $result = &$dbconn->Execute($query);
    if (!$result) return;
    // Get the ID of the item that we inserted
    $cid = $dbconn->PO_Insert_ID($censortable, 'xar_cid');
    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $cid, 'cid');
    // Return the id of the newly created link to the calling process
    return $cid;
}

?>