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
        $msg = xarML('Invalid Parameter Count', join(', ',$invalid), 'admin', 'update', 'sitecloud');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
              SET xar_url   = '" . xarVarPrepForStore($url) . "',
                  xar_title = '" . xarVarPrepForStore($title) . "'
              WHERE xar_id  = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    xarModCallHooks('item', 'update', $id, '');
    return true;
}
?>
