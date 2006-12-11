<?php
/**
 * Create a new itsp plan item
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
 * Create a new itsp course (external)
 *
 * This is a standard adminapi function to create a module item
 *
 * @author the ITSP module development team
 * @param string icoursetitle Title of the course
 * @param int itspid
 * @param int pitemid
 * @param int icourseid The ID of the ITSP course to update
 * @param float icoursecredits
 * @since 21 feb 2006
 * @return int itsp item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_create_icourse($args)
{
    extract($args);
    /* Argument check */
    $invalid = array();
    if (!isset($icoursetitle) || !is_string($icoursetitle)) {
        $invalid[] = 'icoursetitle';
    }
    if (!isset($itspid) || !is_numeric($itspid)) {
        $invalid[] = 'itspid';
    }
    if (!isset($pitemid) || !is_numeric($pitemid)) {
        $invalid[] = 'pitemid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create_icourse', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    $itsp = xarModApiFunc('itsp','user','get',array('itspid'=>$itspid));
    $planid = $itsp['planid'];
    $userid = $itsp['userid'];
    if (!xarSecurityCheck('EditITSP', 1, 'ITSP', "$itspid:$planid:$userid")) {
       return;
    }
    if (!isset($datemodi) || !is_int($datemodi)) {
        $datemodi = time();
    }
    if (!isset($modiby) || !is_int($modiby)) {
        $modiby = xarUserGetVar('uid');
    }

    if (!isset($dateappr) && is_string($dateappr)) {
        $dateappr = strtotime($dateappr);
    }
    if (!isset($icoursedate) && is_string($icoursedate)) {
        $icoursedate = strtotime($icoursedate);
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $icoursestable = $xartable['itsp_itsp_courses'];
    /* Get next ID in table */
    $nextId = $dbconn->GenId($icoursestable);
    $query = "INSERT INTO $icoursestable (
               xar_icourseid,
               xar_pitemid,
               xar_itspid,
               xar_icoursetitle,
               xar_icourseloc,
               xar_icoursedesc,
               xar_icoursecredits,
               xar_icourselevel,
               xar_icourseresult,
               xar_icoursedate,
               xar_dateappr,
               xar_datemodi,
               xar_modiby)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)";
    /* Create an array of values which correspond to the order of the
     * Question marks in the statement above.
     */
    $bindvars = array($nextId,
               $pitemid,
               $itspid,
               $icoursetitle,
               $icourseloc,
               $icoursedesc,
               $icoursecredits,
               $icourselevel,
               $icourseresult,
               $icoursedate,
               $dateappr,
               $datemodi,
               $modiby);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Get the ID of the item that we inserted. */
    $icourseid = $dbconn->PO_Insert_ID($icoursestable, 'xar_icourseid');

    /* Let any hooks know that we have created a new icourse within a planitem
       This is a modification of the planitem of id ITSPid
    */
    $item = $bindvars;
    $item['module'] = 'itsp';
    $item['itemtype'] = $pitemid;
    $item['itemid'] = $itspid;
    xarModCallHooks('item', 'modify', $pitemid, $item);

    /* Return the id of the newly created item to the calling process */
    return $icourseid;
}
?>