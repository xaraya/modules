<?php
/**
 * Get all externally added courses for one itsp
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Get all external courses that have been added to an ITSP by a student
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int numitems $ the number of items to retrieve (default -1 = all)
 * @param int startnum $ start with this item number (default 1)
 * @param int icourseid The id of the course to get
 * @since 30 Aug 2006
 * @return array Array of item, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_get_itspcourse($args)
{
    /* Get arguments from argument array */
    extract($args);
    /* Optional arguments. */

    /* Argument check */
    $invalid = array();

    if (!isset($icourseid) || !is_numeric($icourseid)) {
        $invalid[] = 'icourseid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'get_itspcourse', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */

    if (!xarSecurityCheck('ViewITSP', 1)) {
       return;
    }
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $icoursestable = $xartable['itsp_itsp_courses'];
    $query = "SELECT xar_itspid,
                   xar_pitemid,
                   xar_icoursetitle,
                   xar_icourseloc,
                   xar_icoursedesc,
                   xar_icoursecredits,
                   xar_icourselevel,
                   xar_icourseresult,
                   xar_icoursedate,
                   xar_dateappr,
                   xar_datemodi,
                   xar_modiby
                  FROM $icoursestable
                  WHERE xar_icourseid = $icourseid";
    $result = &$dbconn->Execute($query);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    $item = array();
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    list($itspid, $pitemid, $icoursetitle, $icourseloc, $icoursedesc, $icoursecredits, $icourselevel, $icourseresult,
    $icoursedate, $dateappr, $datemodi,$modiby) = $result->fields;
    if (xarSecurityCheck('ReadITSP', 0, 'ITSP', "$itspid:All:All")) {
        $item = array('itspid'      => $itspid,
                     'pitemid'        => $pitemid,
                     'icoursetitle'   => $icoursetitle,
                     'icourseloc'     => $icourseloc,
                     'icoursedesc'    => $icoursedesc,
                     'icoursecredits' => $icoursecredits,
                     'icourselevel'   => $icourselevel,
                     'icourseresult'  => $icourseresult,
                     'icoursedate'    => $icoursedate,
                     'dateappr'       => $dateappr,
                     'datemodi'       => $datemodi,
                     'modiby'         => $modiby);
    }

    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the items */
    return $item;
}
?>