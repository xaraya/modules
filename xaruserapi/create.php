<?php
/**
 * Create a new presence item
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
 * Create a new presence item
 *
 * @author the SIGMAPersonnel module development team
 * @author MichelV MichelV@xarayahosting.nl
 * @param  $args ['start'] name of the item
 * @param  $args ['end'] number of the item
 * @param  $args ['typeid'] number of the item
 * @returns int
 * @return presence item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @TODO MichelV: ITEMTYPES
 */
function sigmapersonnel_userapi_create($args)
{
    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($start) || !is_numeric($start)) {
        $invalid[] = 'start';
    }
    if (!isset($end) || !is_numeric($end)) {
        $invalid[] = 'end';
    }
    if (!isset($typeid) || !is_numeric($typeid)) {
        $invalid[] = 'typeid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'create', 'SIGMAPersonnel');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check
    if (!xarSecurityCheck('AddSIGMAPresence', 1, 'PresenceItem', "All:All:All")) {
        return;
    }
    // Who has entered this entry?
    $userid = xarUserGetVar('uid');

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $presencetable = $xartable['sigmapersonnel_presence'];
    // Get next ID in table
    $nextId = $dbconn->GenId($presencetable);
    // Add item
    $query = "INSERT INTO $presencetable (
              xar_pid,
              xar_start,
              xar_end,
              xar_personid,
              xar_userid,
              xar_typeid)
            VALUES (?,?,?,?,?,?)";
    // Create an array of values
    $bindvars = array($nextId, $start, $end, $personid, $userid, $typeid);
    $result = &$dbconn->Execute($query,$bindvars);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Get the ID of the item that we inserted.
    $pid = $dbconn->PO_Insert_ID($presencetable, 'xar_pid');

    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'sigmapersonnel';
    $item['itemid'] = $pid;
    $item['itemtype'] = 2; //TODO
    xarModCallHooks('item', 'create', $pid, $item);
    // Return the id of the newly created item to the calling process
    return $pid;
}
?>