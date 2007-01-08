<?php
/**
 * Get all presence items for a certain user
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/418.html
 * @author SIGMAPersonnel module development team
 */
/**
 * get all presence items for one person
 *
 * @author the SIGMApersonnel module development team
 * @author MichelV (MichelV@xarayahosting.nl)
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 *
 * @TODO What is the exact use of this?
 *       Do we want this function for all persons in the db, or just get all items?
 */
function sigmapersonnel_userapi_getallpresence($args)
{
    extract($args);

    if (!xarVarFetch('startnum', 'int:1:', $startnum, 1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('numitems', 'int:1:', $numitems, -1, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('uid', 'int:1:', $uid, xarUserGetVar('uid'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('personid', 'int:1:', $personid, $personid, XARVAR_NOT_REQUIRED)) return;

    // Check input
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getallpresence', 'sigmapersonnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security check
    if (!xarSecurityCheck('ViewSIGMAPresence')) return;
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $presencetable = $xartable['sigmapersonnel_presence'];

    $query = "SELECT xar_pid,
                     xar_userid,
                     xar_personid,
                     xar_start,
                     xar_end,
                     xar_typeid
              FROM $presencetable";

    // Build the Where clause
    // Personid
    if (!empty($personid)) {
        $personid = $personid;
    } elseif (empty($personid) && !empty($uid)) {
        $person = xarModAPIFunc('sigmapersonnel','user', 'getpersonid', array('uid'=>$uid));
        $personid = $person['personid'];
        //echo $personid;
    }

    $query .=" WHERE xar_personid = $personid";
    // Sortorder DESC; first item should give presence, when date is before now
    $query .=" ORDER BY xar_start DESC";

    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($pid, $userid, $personid, $start, $end, $typeid) = $result->fields;
        // Get the typename
        $typename = xarModApiFunc('sigmapersonnel','user','getprestype',array('type'=>$typeid));
        //Order: pid, userid, typeid
        if (xarSecurityCheck('ViewSIGMAPresence', 0, 'PresenceItem', "$pid:All:$typeid")) {
            $items[] = array('pid'      => $pid,
                             'userid'   => $userid,
                             'personid' => $personid,
                             'start'    => $start,
                             'end'      => $end,
                             'typeid'   => $typeid,
                             'typename' => $typename);
        }
    }

    $result->Close();
    // Return the items
    return $items;
}
?>