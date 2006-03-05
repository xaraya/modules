<?php
/**
 * Ephemerids
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Ephemerids Module
 * @link http://xaraya.com/index.php/release/15.html
 * @author Volodymyr Metenchuk
 */
/**
 * delete ephemerids
 * @return bool true on success
 */
function ephemerids_adminapi_delete($args)
{
    extract($args);

    // Argument check
    if (!isset($eid) || !is_numeric($eid)) {
        $msg = xarML('Invalid argument',
                    'eid', 'admin', 'delete', 'ephemerid');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('DeleteEphemerids')) return;
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ephemtable = $xartable['ephem'];

    $query = "DELETE FROM $ephemtable WHERE xar_eid = ?";
    $bindvars = array($eid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Let any hooks know that we have deleted a link
    xarModCallHooks('item', 'delete', $eid, '');
    // Let the calling process know that we have finished successfully
    return true;
}
?>