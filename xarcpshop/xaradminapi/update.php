<?php
/**
 * File: $Id:
 * 
 * Update Shop item
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * @param  $args ['storeid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['storeid'] the new storeid of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarcpshop_adminapi_update($args)
{ 
    extract($args);

    $invalid = array();

    if (!isset($storeid) || !is_numeric($storeid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($nickname) || !is_string($nickname)) {
        $invalid[] = 'nickname';
    }
  if (!isset($toplevel) || !is_string($toplevel)) {
        $toplevel = '';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'xarCPShop');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $item = xarModAPIFunc('xarcpshop','user','get',
                        array('storeid' => (int)$storeid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditxarCPShop', 1, 'Item', "$item[name]:All:$storeid")) {
        return;
    }
    if (!xarSecurityCheck('EditxarCPShop', 1, 'Item', "$name:All:$storeid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $cpstorestable = $xartable['cpstores'];

    $query = "UPDATE $cpstorestable
              SET xar_name = ?,
                  xar_nickname =?,
                  xar_toplevel =?,
                  xar_tid=0
              WHERE xar_storeid = ?";
    $bindvars = array($name, $nickname, $toplevel, (int)$storeid);
    $result = &$dbconn->Execute($query,$bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'xarcpshop';
    $item['itemid'] = $storeid;
    $item['name'] = $name;
    $item['storeid'] = $storeid;
    xarModCallHooks('item', 'update', $storeid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
