<?php
/**
 * create a new URL
 * @param $args['url'] url of the item
 * @returns int
 * @return URL ID on success, false on failure
 */
function ping_adminapi_create($args)
{   // Get arguments from argument array
    extract($args);
    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($url)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
    if(!xarSecurityCheck('Adminping')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pingtable = $xartable['ping'];
    // Get next ID in table
    $nextId = $dbconn->GenId($pingtable);
    // Add item
    $query = "INSERT INTO $pingtable (
              xar_id,
              xar_url,
              xar_method)
            VALUES (?,?,?)";
    $bindvars = array($nextId, $url, $method);
    $result =& $dbconn->Execute($query, $bindvars);
    if (!$result) return;
    // Get the ID of the item that we inserted
    $id = $dbconn->PO_Insert_ID($pingtable, 'xar_id');
    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $id, 'id');
    // Return the id of the newly created link to the calling process
    return $id;
}
?>