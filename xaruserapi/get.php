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
 * get a specific item
 *
 * @author the Ephemerids
 * @param $args['eid'] id of ephemerid
 * @return array item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ephemerids_userapi_get($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if (!isset($eid) || !is_numeric($eid)) {
        $msg = xarML('Invalid parameter',
                    'item ID', 'user', 'get', 'ephemerids');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ephemtable = $xartable['ephem'];

    // Get item
    $query = "SELECT xar_tid,
                     xar_did,
                     xar_mid,
                     xar_yid,
                     xar_content,
                     xar_elanguage
            FROM $ephemtable
            WHERE xar_eid = ?";
    $bindvars = array($eid);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exists');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Obtain the item information from the result set
    list($tid, $did, $mid, $yid, $content, $elanguage) = $result->fields;

    $result->Close();

    // Security Check
    if(!xarSecurityCheck('OverviewEphemerids')) return;

    // Create the item array
    $data = array('eid' => $eid,
                  'tid' => $tid,
                  'did' => $did,
                  'mid' => $mid,
                  'yid' => $yid,
                  'content' => $content,
                  'elanguage' => $elanguage);

    // Return the item array
    return $data;
}
?>