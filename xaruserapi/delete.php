<?php
/**
 * Xaraya MyBookMarks
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MyBookmarks Module
 * @author John Cox et al.
 */
/**
 * delete a bookmark
 *
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
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['mybookmarks'];
    // Delete the item
    $query = "DELETE FROM $table
            WHERE xar_bm_id = ?
            AND xar_user_name = ?";
    $bindvars = array($id, $uid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>