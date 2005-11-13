<?php
/**
 * File: $Id:
 * 
 * Create a new maxercalls item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage maxercalls
 * @author Maxercalls module development team 
 */
/**
 * create a new maxercalls item
 * 
 * @author the Maxercall module development team 
 * @param  $args ['calldate'] date of the call
 * @param  $args ['calltime'] time of the call
 * @param  $args ['owner'] owner indicated
 * @returns int
 * @return maxercalls item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function maxercalls_adminapi_create($args)
{ 

    extract($args); 
    // Argument check
    $invalid = array();
    if (!isset($enteruid) || !is_numeric($enteruid)) {
        $invalid[] = 'enteruid';
    } 
    if (!isset($calldate) || !is_string($calldate)) {
        $invalid[] = 'calldate';
    } 
    if (!isset($calltime) || !is_string($calltime)) {
        $invalid[] = 'calltime';
    } 
    if (!isset($enterts) || !is_string($enterts)) {
        $invalid[] = 'enterts';
    } 
    if (!isset($owner) || !is_numeric($owner)) {
        $invalid[] = 'owner';
    } 
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'Maxercalls');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
/*    if (!xarSecurityCheck('AddMaxercalls', 1, 'Item', "$name:All:All")) {
        return;
    } 
*/    // Get database setup
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 
    $maxercallstable = $xartable['maxercalls']; 
    // Get next ID in table
    $nextId = $dbconn->GenId($maxercallstable);
    // Add item 
    $query = "INSERT INTO $maxercallstable (
              xar_callid,
			  xar_enteruid,
              xar_owner,
              xar_remarks,
			  xar_calldate,
			  xar_calltime,
			  xar_calltext,
			  xar_enterts)
            VALUES (?,?,?,?,?,?,?,?)";
    $bindvars = array($nextId, $enteruid, $owner, $remarks, $calldate, $calltime, $calltext, $enterts);
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return; 
    // Get the ID of the item that we inserted.
    $callid = $dbconn->PO_Insert_ID($maxercallstable, 'xar_callid'); 
    // Let any hooks know that we have created a new item.
    // TODO: evaluate
    $item = $args;
    $item['module'] = 'maxercalls';
    $item['itemid'] = $callid;
    xarModCallHooks('item', 'create', $callid, $item); 
    // Return the id of the newly created item to the calling process
    return $callid;
} 

?>
