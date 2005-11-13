<?php
/**
 * Update an maxercalls item
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage maxercalls
 * @author Example module development team 
 */
/**
 * update an maxercalls item
 * 
 * @author the Example module development team 
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
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
    // The user API function is called.  This takes the item ID which
    // we obtained from the input and gets us the information on the
    // appropriate item.  If the item does not exist we post an appropriate
    // message and return
    $item = xarModAPIFunc('maxercalls',
        'user',
        'get',
        array('callid' => $callid)); 
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
     
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing.
    // However, in this case we had to wait until we could obtain the item
    // name to complete the instance information so this is the first
    // chance we get to do the check
    // Note that at this stage we have two sets of item information, the
    // pre-modification and the post-modification.  We need to check against
    // both of these to ensure that whoever is doing the modification has
    // suitable permissions to edit the item otherwise people can potentially
    // edit areas to which they do not have suitable access
    if (!xarSecurityCheck('EditMaxercalls', 1, 'Item', "$callid:All:$item[enteruid]")) {
        return;
    } 
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $maxercallstable = $xartable['maxercalls']; 
    // Update the item - the formatting here is not mandatory, but it does
    // make the SQL statement relatively easy to read.  Also, separating
    // out the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
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
    $item['module'] = 'maxercalls';
    $item['itemid'] = $callid;
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
