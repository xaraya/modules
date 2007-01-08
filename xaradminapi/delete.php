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
 * delete an maxercalls item
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  $args ['callid'] ID of the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @deprecated feb 2006
 */
function maxercalls_adminapi_delete($args)
{
    // Get arguments from argument array
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

    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing.
    // However, in this case we had to wait until we could obtain the item
    // name to complete the instance information so this is the first
    // chance we get to do the check
    if (!xarSecurityCheck('DeleteMaxercalls', 1, 'Item', "$callid:All:$item[enteruid]")) {
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $maxercallstable = $xartable['maxercalls'];
    // Delete the item
    $query = "DELETE FROM $maxercallstable WHERE xar_callid = ?";
    // The bind variable $exid is directly put in as a parameter.
    $result = &$dbconn->Execute($query,array($callid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have deleted an item.
    $item['module'] = 'maxercalls';
    $item['itemid'] = $callid;
    $item['itemtype'] = 1;
    xarModCallHooks('item', 'delete', $callid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
