<?php
/**
 * Get a linked course
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
 * Get a specific linked course
 *
 * A linked course is a course from the module courses.
 *
 * @author the ITSP module development team
 * @param  int courselinkid id of linked course to get
 * @return array with item, or false on failure
 * @since 28 Aug 2006
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_get_courselink($args)
{
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($courselinkid) || !is_numeric($courselinkid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get_courselink', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $courselinkstable = $xartable['itsp_itsp_courselinks'];
    $query = "SELECT xar_itspid,
                   xar_lcourseid,
                   xar_pitemid,
                   xar_dateappr,
                   xar_datemodi,
                   xar_modiby
              FROM $courselinkstable
              WHERE xar_courselinkid = ?";
    $result = &$dbconn->Execute($query,array($courselinkid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This linked course item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
    list($itspid, $lcourseid, $pitemid, $dateappr, $datemodi, $modiby) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();

    // Get the correct ITSP for this link
    $itsp = xarModApiFunc('itsp','user','get',array('itspid' => $itspid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */
    $userid = $itsp['userid'];
    $planid = $itsp['planid'];
    /* Security check */
    if (!xarSecurityCheck('ReadITSP', 1, 'ITSP', "$itspid:$planid:$userid")) {
        return;
    }
    /* Create the item array */
    $item = array('itspid'        => $itspid,
                  'lcourseid'     => $lcourseid,
                  'pitemid'        => $pitemid,
                  'dateappr'      => $dateappr,
                  'datemodi'      => $datemodi,
                  'modiby'        => $modiby);
    /* Return the item array */
    return $item;
}
?>