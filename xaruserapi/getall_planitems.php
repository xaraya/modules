<?php
/**
 * Get all itsp plans
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
 * Get all ITSP planitems
 *
 * @author the ITSP module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_getall_planitems($args)
{
    extract($args);
    /* Optional arguments.
     * FIXME: (!isset($startnum)) was ignoring $startnum as it contained a null value
     * replaced it with ($startnum == "") (thanks for the talk through Jim S.) NukeGeek 9/3/02
     * if (!isset($startnum)) { */
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
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
            join(', ', $invalid), 'user', 'getall_planitems', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('ViewITSPPlan')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $planitemstable = $xartable['itsp_planitems'];
    /*
     * Get items - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read.  Also, separating out the sql
     * statement from the SelectLimit() command allows for simpler debug
     * operation if it is ever needed
     */
    $query = "SELECT xar_pitemid,
                     xar_pitemname,
                     xar_pitemdesc,
                     xar_pitemrules,
                     xar_credits,
                     xar_mincredit,
                     xar_dateopen,
                     xar_dateclose,
                     xar_datemodi,
                     xar_modiby
              FROM $planitemstable
              ORDER BY xar_pitemname";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pitemid,
             $pitemname,
             $pitemdesc,
             $pitemrules,
             $credits,
             $mincredit,
             $dateopen,
             $dateclose,
             $datemodi,
             $modiby) = $result->fields;
        if (xarSecurityCheck('ViewITSPPlan', 0, 'Plan', "All:$pitemid:All")) {
            $items[] = array('pitemid'    => $pitemid,
                             'pitemname'  => $pitemname,
                             'pitemdesc'  => $pitemdesc,
                             'pitemrules' => $pitemrules,
                             'credits'    => $credits,
                             'mincredit'  => $mincredit,
                             'dateopen'   => $dateopen,
                             'dateclose'  => $dateclose,
                             'datemodi'   => $datemodi,
                             'modiby'     => $modiby);
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