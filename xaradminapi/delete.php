<?php
/**
 * Delete an Legis item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */
/**
 * Delete an Legis item
 *
 * Standard function to delete a module item
 *
 * @author the Legis module development team
 * @param  $args ['cdid'] ID of the item
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function legis_adminapi_delete($args)
{ 
    extract($args);
    if (!isset($cdid) || !is_numeric($cdid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'Legis');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $item = xarModAPIFunc('legis','user','get',array('cdid' => $cdid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    if (!xarSecurityCheck('DeleteLegis', 1, 'Item', "$item[cdtitle]:All:$cdid")) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $LegisCompiledTable = $xarTables['legis_compiled'];
    $query = "DELETE FROM $LegisCompiledTable WHERE xar_cdid = ?";

    $result = &$dbconn->Execute($query,array($cdid));

    if (!$result) return;
    $item['module'] = 'legis';
    $item['itemid'] = $cdid;
    xarModCallHooks('item', 'delete', $cdid, $item);
    //Let's tell all hall members that we have deleted a document
    //notifytype 1 = new document, 2 = validated, 3 = invalidated, 4 = passed, 5 = notpassed, 6 = notvetoed 7 = vetoed 8 = deleted
    if (!xarModAPIFunc('legis','user','notify',
                           array('notifytype'   => 8,
                                 'cdid'         =>$cdid))) return;
    /* Let the calling process know that we have finished successfully */
    return true;
}
?>
