<?php
/**
 * update an site
 * @param $args['id'] the ID of the link
 */
function sitecloud_adminapi_update($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($id)){
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // The user API function is called
    $link = xarModAPIFunc('sitecloud',
                          'user',
                          'get',
                          array('id' => $id));
    if ($link == false) return;

    // Security Check
    if(!xarSecurityCheck('Editsitecloud')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecloudtable = $xartable['sitecloud'];

    // Update the link
    $query = "UPDATE $sitecloudtable
              SET xar_url   = ?,
                  xar_title = ?
              WHERE xar_id  = ?";
    $bindvars = array($url, $title, $id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    xarModCallHooks('item', 'update', $id, '');
    return true;
}
?>