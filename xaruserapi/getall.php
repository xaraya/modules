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
 * get all Ephemerids
 *
 * @author the Ephemerids module development team
 * @param numitems the number of items to retrieve (default -1 = all)
 * @param startnum start with this item number (default 1)
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function ephemerids_userapi_getall($args)
{
    // Get arguments
    extract($args);

    // Optional arguments.
    if ($startnum == "") {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    // Argument check
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid arguments');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $items = array();

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $ephemtable = $xartable['ephem'];
    $query = "SELECT xar_eid,
                     xar_tid,
                     xar_did,
                     xar_mid,
                     xar_yid,
                     xar_content,
                     xar_elanguage
            FROM $ephemtable
            ORDER BY xar_yid";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($exid, $name, $number) = $result->fields;
        if (xarSecurityCheck('OverviewEphemerids', 0)) {
            $items[] = array('eid' => $eid,
                  'tid' => $tid,
                  'did' => $did,
                  'mid' => $mid,
                  'yid' => $yid,
                  'content' => $content,
                  'elanguage' => $elanguage);
        }
    }
    $result->Close();
    // Return the items
    return $items;
}
?>
