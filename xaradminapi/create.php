<?php
/**
 * Create a new itsp plan
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
 * Create a new itsp plan
 *
 * The plan is the largest component in this module
 *
 * @author the ITSP module development team
 * @param  string planname name of the plan
 * @param  string plandesc Description of the plan
 * @param  int credits
 * @return int plan item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_create($args)
{
    extract($args);
    /* Argument check */
    $invalid = array();
    if (!isset($planname) || !is_string($planname)) {
        $invalid[] = 'planname';
    }
    if (!isset($credits) || !is_numeric($credits)) {
        $invalid[] = 'credits';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddITSPPlan', 1, 'Plan', "All:All:All")) {//TODO: check
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
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $planstable = $xartable['itsp_plans'];

    $nextId = $dbconn->GenId($planstable);
    $query = "INSERT INTO $planstable (
               xar_planid,
               xar_planname,
               xar_plandesc,
               xar_planrules,
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
    $bindvars = array($nextId, (string) $planname, $plandesc, $planrules, $credits, $mincredit,
    $dateopen, $dateclose, $datemodi, $modiby);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    $planid = $dbconn->PO_Insert_ID($planstable, 'xar_planid');

    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'itsp';
    $item['itemtype'] = 1;
    $item['itemid'] = $planid;
    xarModCallHooks('item', 'create', $planid, $item);
    /* Return the id of the newly created item to the calling process */
    return $planid;
}
?>