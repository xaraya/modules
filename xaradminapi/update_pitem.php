<?php
/**
 * Update an itsp plan
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Update an itsp plan item
 *
 * @author the ITSP module development team
 * @param  $args ['pitemid'] the ID of the item
 * @param  $args ['pitemname'] the new name of the item
 * @param  $args ['pitemdesc'] the new description of the plan
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @return bool true on success
 */
function itsp_adminapi_update_pitem($args)
{
    extract($args);
    /* Argument check
     */
    $invalid = array();
    if (!isset($pitemid) || !is_numeric($pitemid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($pitemname) || !is_string($pitemname)) {
        $invalid[] = 'name';
    }
    if (!isset($credits) || !is_numeric($credits)) {
        $invalid[] = 'credits';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update_pitem', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get the planitem
     */
    $item = xarModAPIFunc('itsp',
        'user',
        'get_planitem',
        array('pitemid' => $pitemid));
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditITSPPlan', 1, 'Plan', "All:$pitemid:All")) {
        return;
    }
    /* Get database setup */
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $pitemstable = $xartable['itsp_planitems'];
    $query = "UPDATE $pitemstable
            SET xar_pitemname  =?,
                xar_pitemdesc  =?,
                xar_pitemrules =?,
                xar_credits   =?,
                xar_mincredit =?,
                xar_dateopen  =?,
                xar_dateclose =?,
                xar_datemodi  =?,
                xar_modiby    =?
            WHERE xar_pitemid  =?";
    $bindvars = array($pitemname, $pitemdesc, $pitemrules, $credits, $mincredit,
    $dateopen, $dateclose, $datemodi, $modiby, $pitemid);
    $result = &$dbconn->Execute($query,$bindvars);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have updated an item.  As this is an
     * update hook we're passing the updated $item array as the extra info
     */
    $item['module'] = 'itsp';
    $item['itemid'] = $pitemid;
    $item['pitemname'] = $pitemname;
    $item['credits'] = $credits;
    $item['itemtype'] = 3;
    xarModCallHooks('item', 'update', $pitemid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>