<?php
/**
 * delete
 * @param $args['id'] ID of the link
 * @returns bool
 * @return true on success, false on failure
 */
function mybookmarks_userapi_delete($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($id)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // Security Check
    if(!xarSecurityCheck('Viewmybookmarks')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['mybookmarks'];
    // Delete the item
    $query = "DELETE FROM $table
            WHERE xar_bm_id = ?";
    $bindvars = array($id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>