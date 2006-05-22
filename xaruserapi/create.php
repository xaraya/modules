<?php
/**
 * Create a new itsp item
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
 * Create a new itsp item
 *
 * This is a standard userapi function to create a module item
 *
 * @author the ITSP module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return itsp item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_userapi_create($args)
{
    extract($args);
    // Argument check
    $invalid = array();
    if (empty($planid) || !is_numeric($planid)) {
        $invalid['planid'] = 1;
        $number = '';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'userapi', 'create', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddITSP', 1, 'ITSP', "All:$planid:$userid")) {
        return;
    }
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $itsptable = $xartable['itsp_itsp'];
    $nextId = $dbconn->GenId($itsptable);
    $query = "INSERT INTO $itsptable (
               xar_itspid,
               xar_userid,
               xar_planid,
               xar_itspstatus,
               xar_datesubm,
               xar_dateappr,
               xar_datecertreq,
               xar_datecertaward,
               xar_datemodi,
               xar_modiby)
            VALUES (?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($nextId, $userid, $planid, $itspstatus, $datesubm, $dateappr, $datecertreq,
                      $datecertaward, $datemodi, $modiby);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Get the ID of the item that we inserted.
     */
    $itspid = $dbconn->PO_Insert_ID($itsptable, 'xar_itspid');

    /* Let any hooks know that we have created a new item. As this is a
     * create hook we're passing 'exid' as the extra info, which is the
     * argument that all of the other functions use to reference this
     * item
     * TODO: evaluate
     * xarModCallHooks('item', 'create', $exid, 'exid');
     */
    $item = $args;
    $item['module'] = 'itsp';
    $item['itemid'] = $itspid;
    $item['itemtype'] = 2;
    xarModCallHooks('item', 'create', $itspid, $item);
    /* Return the id of the newly created item to the calling process */
    return $itspid;
}
?>