<?php
/**
 * Delete an ID
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * Delete an ID
 * 
 * @param $rid ID
 * 
 * Original Author of file: John Cox via phpMailer Team
 * @author Release module development team
 */
function release_adminapi_deleteid($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($rid)) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // The user API function is called
    $link = xarModAPIFunc('release', 'user', 'getid',
                         array('eid' => $eid));

    if ($link == false) {
        $msg = xarML('No Such Release ID Present');
        xarErrorSet(XAR_USER_EXCEPTION, 
                    'MISSING_DATA',
                     new DefaultUserException($msg));
        return; 
    }

    // Security Check
    if(!xarSecurityCheck('DeleteRelease')) return;

    // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Delete the item
    $query = "DELETE FROM $releasetable
            WHERE xar_eid = ?";
    $result =& $dbconn->Execute($query,array($eid));
    if (!$result) return;

    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $eid, '');

    // Let the calling process know that we have finished successfully
    return true;
}

?>