<?php
/**
 * Update an maxercalls item
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * update an maxercalls item
 *
 * @author the Example module development team
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function maxercalls_adminapi_update($args)
{
    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($callid) || !is_numeric($callid)) {
        $invalid[] = 'callid';
    }
    if (!isset($remarks) || !is_string($remarks)) {
        $invalid[] = 'remarks';
    }
    if (!isset($owner) || !is_numeric($owner)) {
        $invalid[] = 'owner';
    }
    if (!isset($enterts) || !is_string($enterts)) {
        $invalid[] = 'enterts';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'Maxercalls');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get this call
    $item = xarModAPIFunc('maxercalls',
        'user',
        'get',
        array('callid' => $callid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditMaxercalls', 1, 'Call', "$callid:All:$item[enteruid]")) {
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $maxercallstable = $xartable['maxercalls'];
    // Update the item
    $query = "UPDATE $maxercallstable
            SET xar_callid = ?,
              xar_enteruid = ?,
              xar_owner = ?,
              xar_remarks = ?,
              xar_calldatetime = ?,
              xar_enterts = ?
            WHERE xar_callid = ?";
    $bindvars = array($callid, $enteruid, $owner, $remarks, $calldatetime, $enterts, $callid);
    $result = &$dbconn->Execute($query,$bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item = $args;
    $item['module'] = 'maxercalls';
    $item['itemid'] = $callid;
    $item['itemtype'] = 1;
    $item['enteruid'] = $enteruid;
    $item['owner'] = $owner;
    $item['remarks'] = $remarks;
    $item['calldatetime'] = $calldatetime;
    $item['enterts'] = $enterts;
    xarModCallHooks('item', 'update', $callid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
