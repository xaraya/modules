<?php
/**
 * Xaraya BBCode
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BBCode Module
 * @link http://xaraya.com/index.php/release/778.html
 * @author John Cox
*/
/**
 * delete
 * @param $args['id'] ID of the bbcode
 * @returns bool
 * @return true on success, false on failure
 */
function bbcode_adminapi_delete($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($id)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    // The user API function is called
    $smiley = xarModAPIFunc('bbcode',
                          'user',
                          'get',
                          array('id' => $id));
    if (empty($smiley)) {
        $msg = xarML('No code present');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return; 
    }
    // Security Check
    if(!xarSecurityCheck('EditBBCode')) return;
    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['bbcode'];
    // Delete the item
    $query = "DELETE FROM $table
            WHERE xar_id = ?";
    $bindvars = array($id);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $id, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>