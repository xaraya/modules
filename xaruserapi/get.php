<?php
/**
 * Get a specific ITSP
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
 * Get a specific ITSP
 *
 * Standard function of a module to retrieve a specific item
 *
 * @author the ITSP module development team
 * @param  $args ['itspid'] id of itsp item to get
 * @param  $args ['userid'] id of the user to get the itsp for
 * @return array with item, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function itsp_userapi_get($args)
{
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if ((!isset($itspid) || !is_numeric($itspid)) && (!isset($userid) || !is_numeric($userid))) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'get', 'ITSP');
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
    $itsptable = $xartable['itsp_itsp'];
    /* Get item by userid or itspid */
    // TODO: improve or split?
    $query = "SELECT xar_itspid,
               xar_userid,
               xar_planid,
               xar_itspstatus,
               xar_datesubm,
               xar_dateappr,
               xar_datecertreq,
               xar_datecertaward,
               xar_datemodi,
               xar_modiby
              FROM $itsptable";
    if (!empty($itspid) && is_numeric($itspid)) {
        $query .= " WHERE xar_itspid = $itspid";
    } elseif (isset($userid)) {
        $query .= " WHERE xar_userid = $userid";
    }
    $result = &$dbconn->Execute($query);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
    list($itspid, $userid, $planid, $itspstatus, $datesubm, $dateappr, $datecertreq, $datecertaward, $datemodi, $modiby) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Security check */
    if (!xarSecurityCheck('ReadITSP', 1, 'ITSP', "$itspid:$planid:$userid")) {
        return;
    }
    /* Create the item array */
    $item = array('itspid'        => $itspid,
                  'userid'        => $userid,
                  'planid'        => $planid,
                  'itspstatus'    => $itspstatus,
                  'datesubm'      => $datesubm,
                  'dateappr'      => $dateappr,
                  'datecertreq'   => $datecertreq,
                  'datecertaward' => $datecertaward,
                  'datemodi'      => $datemodi,
                  'modiby'        => $modiby);
    /* Return the item array */
    return $item;
}
?>