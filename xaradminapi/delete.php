<?php
/**
 * File: $Id:
 * 
 * Delete an xarcpshop item
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * @param  $args ['storeid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarcpshop_adminapi_delete($args)
{ 
    extract($args);
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($storeid) || !is_numeric($storeid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'xarCPShop');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 
     $item = xarModAPIFunc('xarcpshop','user','get',
        array('storeid' => $storeid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('DeletexarCPShop', 1, 'Item', "$item[name]:All:$storeid")) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $cpstorestable = $xartable['cpstores'];
    $query = "DELETE FROM $cpstorestable WHERE xar_storeid = ?";
    // The bind variable $storeid is directly put in as a parameter.
    $result = &$dbconn->Execute($query,array($storeid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    $item['module'] = 'xarcpshop';
    $item['itemid'] = $storeid;
    xarModCallHooks('item', 'delete', $storeid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
