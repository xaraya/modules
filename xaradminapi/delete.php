<?php
/**
 * Ephemerids Module
 *
 * @package modules
 * @subpackage ephemerids module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
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
    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();
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