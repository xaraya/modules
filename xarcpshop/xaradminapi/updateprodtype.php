<?php
/**
 * File: $Id:
 * 
 * Update product type
 *
 * @copyright (C) 2004 by Jo Dalle Nogare
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.athomeandabout.com
 *
 * @subpackage xarcpshop
 * @author jojodee@xaraya.com
 */
/**
 * @param  $args ['prodtypeid'] the ID of the item
 * @param  $args ['description'] the new description of the item
 * @param  $args ['prodtype'] the new prodtype of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function xarcpshop_adminapi_updateprodtype($args)
{ 
    extract($args);

    $invalid = array();
    
    if (!isset($prodtypeid) || !is_numeric($prodtypeid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($prodtype) || !is_string($prodtype)) {
        $invalid[] = 'prodtype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updateprodtype', 'xarCPShop');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $item = xarModAPIFunc('xarcpshop','user','getprodtype',
                        array('prodtypeid' => $prodtypeid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditxarCPShop', 1, 'Item', "$item[prodtypeid]:All:All")) {
        return;
    }
    if (!xarSecurityCheck('EditxarCPShop', 1, 'Item', "$prodtypeid:All:All")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $cptypestable = $xartable['cptypes'];

    $query = "UPDATE $cptypestable
              SET xar_prodtype =?,
                  xar_description = ?
              WHERE xar_prodtypeid = ?";
    $bindvars = array($prodtype, $description, (int)$prodtypeid);
    $result = &$dbconn->Execute($query,$bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'xarcpshop';
    $item['itemid'] = $prodtypeid;
    $item['description'] = $description;
    $item['prodtype'] = $prodtype;
    xarModCallHooks('item', 'update', $prodtypeid, $item);
    // Let the calling process know that we have finished successfully
    return true;
}

?>
