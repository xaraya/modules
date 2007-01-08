<?php
/**
 * Get all itsp plans
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Get all ITSP plans
 *
 * @author the ITSP module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_getall_plans($args)
{
    extract($args);
    /* Optional arguments.*/
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    if (!isset($enddate)) {
        $enddate = 0;
    }
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     * Note : since we have several arguments we want to check here, we'll
     * report all those that are invalid at the same time...
     */
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall_plans', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    // Security check
    if (!xarSecurityCheck('ViewITSPPlan')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $planstable = $xartable['itsp_plans'];
    // Get items
    $query = "SELECT xar_planid,
                     xar_planname,
                     xar_plandesc,
                     xar_planrules,
                     xar_credits,
                     xar_mincredit,
                     xar_dateopen,
                     xar_dateclose,
                     xar_datemodi,
                     xar_modiby
              FROM $planstable";

    if (is_numeric($enddate) && ($enddate > 0) ) {
        $query .= " WHERE xar_dateclose > $enddate OR xar_dateclose IS NULL OR xar_dateclose = 0";
    }
    $query .= " ORDER BY xar_planname ";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($planid, $planname, $plandesc, $planrules, $credits,
         $mincredit, $dateopen, $dateclose, $datemodi, $modiby) = $result->fields;
        if (xarSecurityCheck('ViewITSPPlan', 0, 'Plan', "$planid:All")) {

            $items[] = array('planid'      => $planid,
                             'planname'    => $planname,
                             'plandesc'    => $plandesc,
                             'planrules'   => $planrules,
                             'credits'     => $credits,
                             'mincredit'   => $mincredit,
                             'dateopen'    => (int)$dateopen,
                             'dateclose'   => (int)$dateclose,
                             'datemodi'    => $datemodi,
                             'modiby'      => $modiby);
        }
    }
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the items */
    return $items;
}
?>