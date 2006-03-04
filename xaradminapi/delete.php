<?php
/**
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
 */
/**
 * delete an censored word
 *
 * @param  $args ['cid'] ID of the censored word to delete
 * @return bool true on success, false on failure
 */
function censor_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($cid)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)_#(2).php', 'admin', 'delete', 'censor');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // The user API function is called
    $link = xarModAPIFunc('censor', 'user', 'get', array('cid' => $cid));
    if ($link == false) {
        $msg = xarML('No Such Word Present');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
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
            WHERE xar_cid = ?";
    $bindvars = array($cid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $cid, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>