<?php
/**
 * Get the current presence of a person
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author SIGMAPersonnel module development team
 */
/**
 * get the presence of a person at a given date
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param dtasked $ the date and time we define presence for
 * @return int id of the presence type or false (no presence found)
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function sigmapersonnel_userapi_presencenow($args)
{
    extract($args);

    if (!xarVarFetch('uid',      'int:1:', $uid, xarUserGetVar('uid'), XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('personid', 'int:1:', $personid, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('dtasked',  'isset', $dtasked, time(), XARVAR_NOT_REQUIRED)) return;
    // Safe conversion...?
    if (is_string($dtasked)) {
        $dtasked = strtotime($dtasked);
    }

    // Check input
    $invalid = array();
    // Argument check. These are nonsense, set by xarVarFetch above.
    // One of these needs to be set
    if (empty($uid) && empty($personid)) {
        $invalid[] = xarML('personid and uid combination');
    }
    if (!isset($dtasked)) {
        $invalid[] = 'dtasked';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'presencenow', 'sigmapersonnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    $presencenow = '';
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

    // Build the WHERE clause
    if (!empty($personid)) {
        $personid = $personid;
    } elseif (empty($personid) && !empty($uid)) {
        $person = xarModAPIFunc('sigmapersonnel','user', 'getpersonid', array('uid'=>$uid));
        $personid = $person['personid'];
        //echo $personid;
    }
    if (!empty($personid)) {
        $query .=" WHERE xar_personid = $personid";
        // Sortorder DESC; first item should give presence, when date is before now
        $query .=" ORDER BY xar_start DESC";
        // Move on and get the presence
        $result =& $dbconn->Execute($query);
        // Check for an error with the database code, adodb has already raised
        // the exception so we just return
        if (!$result) return;

        for (; !$result->EOF; $result->MoveNext()) {
            list($pid, $userid, $personid, $start, $end, $typeid) = $result->fields;

            //Order: pid, userid, typeid
            if (xarSecurityCheck('ViewSIGMAPresence', 0, 'PresenceItem', "$pid:All:$typeid")) {
                $items[] = array('pid'      => $pid,
                                 'userid'   => $userid,
                                 'personid' => $personid,
                                 'start'    => $start,
                                 'end'      => $end,
                                 'typeid'   => $typeid);
            }
        }

        $result->Close();
        // Loop over the array and find the entry that describes the presence at the date asked.
        if (!empty($items)) {
            foreach ($items as $item) {
                // We want to get the first item here that fits the needs
                if (xarSecurityCheck('ReadSIGMAPresence', 0, 'PresenceItem', "$item[pid]:All:All")) {// TODO: Improve this
                    if (($item['start'] < $dtasked) && ($item['end'] > $dtasked)) {
                    $presencenow = $item['typeid'];
                    return $presencenow;
                    }
                } else {
                    $presencenow = '';
                }
            }

            // Return the items
            return $presencenow;
        } else {
            // We have an empty array
            return false;
        }
    }// Empty personid
    // No result, return false
    return false;
}
?>