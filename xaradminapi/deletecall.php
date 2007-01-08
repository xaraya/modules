<?php
/**
 * Delete an maxercalls item
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
 * delete a maxercall
 *
 * @author the Maxercalls module development team
 * @param  $args ['callid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function maxercalls_adminapi_deletecall($args)
{
    extract($args);
    // Argument check
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
    if (!xarSecurityCheck('DeleteMaxercalls', 1, 'Call', "$callid:All:$item[enteruid]")) {
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
    $item['itemtype']= 1;
    xarModCallHooks('item', 'delete', $callid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
