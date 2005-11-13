<?php
/**
 * delete a maxercall
 * 
 * @author the Maxercalls module development team 
 * @param  $args ['callid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function maxercalls_adminapi_deletecall($args)
{ 
    extract($args); 
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($callid) || !is_numeric($callid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'Maxercalls');
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
     
    // Security check 
    if (!xarSecurityCheck('DeleteMaxercalls', 1, 'Item', "$callid:All:$item[enteruid]")) {
        return;
    } 
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables(); 
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $maxercallstable = $xartable['maxercalls']; 
    // Delete the item
    $query = "DELETE FROM $maxercallstable WHERE xar_callid = ?";
    // The bind variable $exid is directly put in as a parameter.
    $result = &$dbconn->Execute($query,array($callid)); 
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return; 
    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    $item['module'] = 'maxercalls';
    $item['itemid'] = $callid;
    xarModCallHooks('item', 'delete', $callid, $item); 
    // Let the calling process know that we have finished successfully
    return true;
} 

?>
