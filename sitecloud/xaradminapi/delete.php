<?
/**
 * delete an site
 * @param $args['id'] ID of the headline
 * @returns bool
 * @return true on success, false on failure
 */
function sitecloud_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($id)) {
        $msg = xarML('Invalid Parameter Count', join(', ',$invalid), 'admin', 'delete', 'sitecloud');
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
	if(!xarSecurityCheck('Deletesitecloud')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $sitecloudtable = $xartable['sitecloud'];
    // Delete the item
    $query = "DELETE FROM $sitecloudtable
              WHERE xar_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>