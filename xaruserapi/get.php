<?php
/**
 * File: $Id:
 * 
 * Get a specific shop
 * 
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * get a specific item
 * 
 * @param  $args ['storeid'] id of xarcpshop item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function xarcpshop_userapi_get($args)
{
    extract($args);

    if (!isset($storeid) || !is_numeric($storeid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'xarCPShop');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
 
    $cpstorestable = $xartable['cpstores'];
 
    $query = "SELECT xar_storeid,
                   xar_name,
                   xar_nickname,
                   xar_toplevel
            FROM $cpstorestable
            WHERE xar_storeid = ?";
    $result = &$dbconn->Execute($query,array($storeid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    /*if ($result->EOF) {
        $result->Close();
        $msg = xarML('This shop does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }*/
    // Obtain the item information from the result set
    list($storeid, $name, $nickname, $toplevel) = $result->fields;
    $result->Close();
    if (!xarSecurityCheck('ReadxarCPShop', 1, 'Item', "$name:All:$storeid")) {
        return;
    }
    // Create the item array
    $item = array('storeid' => $storeid,
                  'name' => $name,
                  'nickname' => $nickname,
                  'toplevel' => $toplevel);
    // Return the item array
    return $item;
}

?>
