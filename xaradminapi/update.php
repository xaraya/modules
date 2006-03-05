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
 * update ephemerids
 */
function ephemerids_adminapi_update($args)
{
    // Get arguments
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($eid)) ||
        (!isset($tid)) ||
        (!isset($did)) ||
        (!isset($mid)) ||
        (!isset($yid)) ||
        (!isset($content))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;
    $elanguage = 'all';

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ephemtable = $xartable['ephem'];

    $query = "UPDATE $ephemtable
              SET xar_yid       = ?,
                  xar_mid       = ?,
                  xar_did       = ?,
                  xar_tid       = ?,
                  xar_content   = ?,
                  xar_elanguage = ?
              WHERE xar_eid = ?";
    $bindvars = array($yid, $mid, $did, $tid, $content, $elanguage, $eid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    return true;
}
?>
