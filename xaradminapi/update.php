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
 * Update an itsp plan
 *
 * @author the ITSP module development team
 * @param  $args ['planid'] the ID of the item
 * @param  $args ['planname'] the new name of the item
 * @param  $args ['plandesc'] the new description of the plan
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @return bool true on success
 */
function itsp_adminapi_update($args)
{
    extract($args);
    /* Argument check
     */
    $invalid = array();
    if (!isset($planid) || !is_numeric($planid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($planname) || !is_string($planname)) {
        $invalid[] = 'name';
    }
    if (!isset($credits) || !is_numeric($credits)) {
        $invalid[] = 'credits';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* The user API function is called.  This takes the item ID which
     * we obtained from the input and gets us the information on the
     * appropriate item.  If the item does not exist we post an appropriate
     * message and return
     */
    $item = xarModAPIFunc('itsp',
        'user',
        'get_plan',
        array('planid' => $planid));
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditITSPPlan', 1, 'Plan', "$planid:All:All")) {
        return;
    }
    /* Get database setup */
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $planstable = $xartable['itsp_plans'];
    $query = "UPDATE $planstable
            SET xar_planname  =?,
                xar_plandesc  =?,
                xar_planrules =?,
                xar_credits   =?,
                xar_mincredit =?,
                xar_dateopen  =?,
                xar_dateclose =?,
                xar_datemodi  =?,
                xar_modiby    =?
            WHERE xar_planid  =?";
    $bindvars = array($planname, $plandesc, $planrules, $credits, $mincredit,
    $dateopen, $dateclose, $datemodi, $modiby, $planid);
    $result = &$dbconn->Execute($query,$bindvars);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have updated an item.  As this is an
     * update hook we're passing the updated $item array as the extra info
     */
    $item['module'] = 'itsp';
    $item['itemid'] = $planid;
    $item['planname'] = $planname;
    $item['credits'] = $credits;
    $item['itemtype'] = 1;
    xarModCallHooks('item', 'update', $planid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>