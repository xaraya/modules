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
 * add ephemerids to db
 */
function ephemerids_adminapi_add($args)
{
    // Get arguments
    extract($args);

    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if ((!isset($tid)) ||
        (!isset($did)) ||
        (!isset($mid)) ||
        (!isset($yid)) ||
        (!isset($content))) {
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // Security Check
    if(!xarSecurityCheck('AddEphemerids')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $elanguage = 'all';
    $ephemtable = $xartable['ephem'];
    $nextId = $dbconn->GenId($ephemtable);
    $query = "INSERT INTO $ephemtable (xar_eid,
                                       xar_tid,
                                       xar_did,
                                       xar_mid,
                                       xar_yid,
                                       xar_content,
                                       xar_elanguage)
                                VALUES (?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?,
                                        ?)";
    $bindvars = array($nextId, $tid, $did, $mid, $yid, $content, $elanguage);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    // Get the ID of the item that we inserted.
    $eid = $dbconn->PO_Insert_ID($ephemtable, 'xar_eid');
    // Let any hooks know that we have created a new link
    xarModCallHooks('item', 'create', $eid, 'eid');
    // Return the id of the newly created link to the calling process
    return $eid;
}
?>
