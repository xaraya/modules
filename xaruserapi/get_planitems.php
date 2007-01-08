<?php
/**
 * Get all planitems for a plan
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
 * Get all planitems for a planid
 *
 * @author the ITSP module development team
 *
 * @param planid id of the plan to get the items for
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_get_planitems($args)
{
    extract($args);
    // Optional arguments.
    if (!xarVarFetch('planid', 'id', $planid, '', XARVAR_NOT_REQUIRED)) return;

    $items = array();
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('ViewITSPPlan')) return;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planlinkstable = $xartable['itsp_planlinks'];
    /*
     * Get items - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read.  Also, separating out the sql
     * statement from the SelectLimit() command allows for simpler debug
     * operation if it is ever needed
     */
    $query = "SELECT xar_pitemid,
                     xar_order,
                     xar_datemodi,
                     xar_modiby
              FROM $planlinkstable
              WHERE xar_planid = ?
              ORDER BY xar_order";

    $result = &$dbconn->Execute($query,array($planid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }

    // Put items into result array.
    for (; !$result->EOF; $result->MoveNext()) {
        list($pitemid,
             $order,
             $datemodi,
             $modiby) = $result->fields;
        if (xarSecurityCheck('ViewITSPPlan', 0, 'Plan', "$planid:$pitemid")) {
            $items[] = array('pitemid'    => $pitemid,
                             'order'      => $order,
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