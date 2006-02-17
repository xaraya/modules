<?php
/**
 * Get a specific call
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * get a specific call
 *
 * @author the Maxercalls module development team
 * @param  $args ['callid'] id of maxercalls item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function maxercalls_userapi_get($args)
{
    extract($args);
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($callid) || !is_numeric($callid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'Maxercalls');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $maxercallstable = $xartable['maxercalls'];
    $query = "SELECT xar_callid,
                   xar_calldate,
                   xar_calltime,
                   xar_calltext,
                   xar_owner,
                   xar_remarks,
                   xar_enterts,
                   xar_enteruid
            FROM $maxercallstable
            WHERE xar_callid = ?";
    $result = &$dbconn->Execute($query,array($callid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    // Obtain the item information from the result set
    list($callid, $calldate, $calltime, $calltext, $owner, $remarks, $enterts, $enteruid) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Security check
    if (!xarSecurityCheck('ReadMaxercalls', 1, 'Call', "$callid:All:$enteruid")) {
        return;
    }
    // Create the item array
    $item = array('callid' => $callid,
                'calldate' => $calldate,
                'calltime' => $calltime,
                'calltext' =>$calltext,
                'owner' => $owner,
                'remarks' => $remarks,
                'enterts' => $enterts,
                'enteruid' => $enteruid);
    // Return the item array
    return $item;
}
?>