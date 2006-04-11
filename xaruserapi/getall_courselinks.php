<?php
/**
 * Get all courses for one itsp
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
 * Get all courses that have been added to an ITSP by a student
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int numitems $ the number of items to retrieve (default -1 = all)
 * @param int startnum $ start with this item number (default 1)
 * @param int itspid The id of the ITSP to look for
 * @param int pitemid The id of the planitem to look (OPTIONAL)
 * @return array Empty, of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_getall_courselinks($args)
{
    /* Get arguments from argument array */
    extract($args);
    /* Optional arguments. */
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    /* Argument check */
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (!isset($itspid) || !is_numeric($itspid)) {
        $invalid[] = 'itspid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall_courselinks', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('ViewITSP')) return;
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $courselinkstable = $xartable['itsp_itsp_courselinks'];
    $query = "SELECT xar_courselinkid,
                   xar_lcourseid,
                   xar_pitemid,
                   xar_dateappr,
                   xar_datemodi,
                   xar_modiby
              FROM $courselinkstable
              WHERE xar_itspid = $itspid";
    if (!empty($pitemid)) {
       $query .= " AND xar_pitemid = $pitemid ";
    }
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Put items into result array.  Note that each item is checked
     * individually to ensure that the user is allowed *at least* OVERVIEW
     * access to it before it is added to the results array.
     * If more severe restrictions apply, e.g. for READ access to display
     * the details of the item, this *must* be verified by your function.
     */
    for (; !$result->EOF; $result->MoveNext()) {
        list($courselinkid, $lcourseid, $pitemid, $dateappr,
               $datemodi,$modiby) = $result->fields;
        if (xarSecurityCheck('ViewITSP', 0, 'ITSP', "$itspid:All:All")) {
            $items[] = array('courselinkid' => $courselinkid,
                             'lcourseid'    => $lcourseid,
                             'pitemid'      => $pitemid,
                             'dateappr'     => $dateappr,
                             'datemodi'     => $datemodi,
                             'modiby'       => $modiby);
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