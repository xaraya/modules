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
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @since 21 feb 2006
 * @return int itsp item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_create_courselink($args)
{
    extract($args);
    /* Argument check */
    $invalid = array();
    if (!isset($lcourseid) || !is_string($lcourseid)) {
        $invalid[] = 'lcourseid';
    }
    if (!isset($itspid) || !is_numeric($itspid)) {
        $invalid[] = 'itspid';
    }
    if (!isset($pitemid) || !is_numeric($pitemid)) {
        $invalid[] = 'pitemid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create_courselink', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('EditITSP', 1, 'ITSP', "$itspid:All:All")) {//TODO: check
        return;
    }
    if (!empty($dateappr) && is_string($dateappr)) {
        $dateappr = strtotime($dateappr);
    }
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $courselinkstable = $xartable['itsp_itsp_courselinks'];
    /* Get next ID in table */
    $nextId = $dbconn->GenId($courselinkstable);
    $query = "INSERT INTO $courselinkstable (
               xar_courselinkid,
               xar_lcourseid,
               xar_itspid,
               xar_pitemid,
               xar_dateappr,
               xar_datemodi,
               xar_modiby)
            VALUES (?,?,?,?,?,?,?)";
    /* Create an array of values which correspond to the order of the
     * Question marks in the statement above.
     */
    $bindvars = array($nextId,
               $lcourseid,
               $itspid,
               $pitemid,
               $dateappr,
               $datemodi,
               $modiby);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Get the ID of the item that we inserted. */
    $courselinkid = $dbconn->PO_Insert_ID($courselinkstable, 'xar_courselinkid');
    // Let any hooks know that we have created a new item.
    $item = $bindvars;
    $item['module'] = 'itsp';
    $item['itemtype'] = 4;
    $item['itemid'] = $pitemid;
    xarModCallHooks('item', 'create', $pitemid, $item);

    /* Return the id of the newly created item to the calling process */
    return $courselinkid;
}
?>