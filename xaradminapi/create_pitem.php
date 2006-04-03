<?php
/**
 * Create a new itsp plan item
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
 * Create a new itsp plan item
 *
 * This API creates a plan item. Plan items are part of the education plan, and can be seen as the seperate parts.
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  string 'pitemname' the name of the item to be created
 * @param  string 'pitemdesc' the description of the item to be created
 * @param int 'mincredit'
 * @param int 'credits'
 * @param int 'rule_cat'
 * @param int 'rule_level'
 * @param int 'rule_type'
 * @param string rule_source The source for the courses. This will tell the ITSP module where the data for the planitems
                             is coming from.
 * @param  int mincredit number credits to be obtained
 * @return int itsp item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_create_pitem($args)
{
    extract($args);
    /* Argument check */
    $invalid = array();
    if (!isset($pitemname) || !is_string($pitemname)) {
        $invalid[] = 'pitemname';
    }
    if (!isset($mincredit) || !is_numeric($mincredit)) {
        $invalid[] = 'mincredit';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create_pitem', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check */
    if (!xarSecurityCheck('AddITSPPlan', 1, 'Plan', "All:All")) {
        return;
    }
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    // Transform dates to int(11)
    if (!empty($dateopen) && is_string($dateopen)) {
        $dateopen = strtotime($dateopen);
    }
    if (!empty($dateclose) && is_string($dateclose)) {
        $dateclose = strtotime($dateclose);
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planitemstable = $xartable['itsp_planitems'];
    /* Get next ID in table */
    $nextId = $dbconn->GenId($planitemstable);
    $query = "INSERT INTO $planitemstable (
               xar_pitemid,
               xar_pitemname,
               xar_pitemdesc,
               xar_pitemrules,
               xar_credits,
               xar_mincredit,
               xar_dateopen,
               xar_dateclose,
               xar_datemodi,
               xar_modiby)
            VALUES (?,?,?,?,?,?,?,?,?,?)";
    /* Create an array of values which correspond to the order of the
     * Question marks in the statement above.
     */
    $bindvars = array($nextId, (string) $pitemname, $pitemdesc, $pitemrules, $credits, $mincredit,
    $dateopen, $dateclose, $datemodi, $modiby);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Get the ID of the item that we inserted. */
    $pitemid = $dbconn->PO_Insert_ID($planitemstable, 'xar_pitemid');

    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'itsp';
    $item['itemtype'] = 3;
    $item['itemid'] = $pitemid;
    xarModCallHooks('item', 'create', $pitemid, $item);
    /* Return the id of the newly created item to the calling process */
    return $pitemid;
}
?>