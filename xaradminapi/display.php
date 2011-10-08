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
 * @return an array containing ephemerids data
 *
 */
function ephemerids_adminapi_display()
{
    // Security Check
    if(!xarSecurityCheck('EditEphemerids')) return;

    $dbconn =& xarDB::getConn();
    $xartable =& xarDB::getTables();

    $ephemtable = $xartable['ephem'];

    $query = "SELECT xar_eid,
                     xar_tid,
                     xar_did,
                     xar_mid,
                     xar_yid,
                     xar_content,
                     xar_elanguage
    FROM $ephemtable ORDER BY xar_eid DESC";

    $result =& $dbconn->Execute($query);
    if (!$result) return;

    $resarray = array();

    while(list($eid, $tid, $did, $mid, $yid, $content, $elanguage) = $result->fields) {
    $result->MoveNext();

    $resarray[] = array('eid' => $eid,
                'tid' => $tid,
                'did' => $did,
                'mid' => $mid,
                'yid' => $yid,
                'content' => $content,
                'elanguage' => $elanguage);
    }
    $result->Close();

    return $resarray;
}
?>