<?php
/**
 * Create a new itsp plan item
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */
/**
 * Create a new itsp plan item
 *
 * This is a standard adminapi function to create a module item
 *
 * @author the ITSP module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return itsp item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_create_pitem($args)
{
    extract($args);
    /* Argument check */
    $invalid = array();
    if (!isset($pitemname) || !is_string($pitemname)) {
        $invalid[] = 'pitemname';
    }
    if (!isset($credits) || !is_numeric($credits)) {
        $invalid[] = 'credits';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AddITSPPlan', 1, 'Plan', "All:All:All")) {//TODO: check
        return;
    }
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $planitemstable = $xartable['itsp_planitems'];
    /* Get next ID in table - this is required prior to any insert that
     * uses a unique ID, and ensures that the ID generation is carried
     * out in a database-portable fashion
     */
    $nextId = $dbconn->GenId($planitemstable);
    $query = "INSERT INTO $planitemstable (
               xar_pitemid,
               xar_pitemname,
               xar_pitemdesc,
               xar_pitemrules,
               xar_credits,
               xar_mincredit,
               xar_dateopen,
               xar_dateclose,
               xar_datemodi,
               xar_modiby)
            VALUES (?,?,?,?,?,?,?,?,?,?)";
    /* Create an array of values which correspond to the order of the
     * Question marks in the statement above.
     */
    $bindvars = array($nextId, (string) $pitemname, $pitemdesc, $pitemrules, $credits, $mincredit,
    $dateopen, $dateclose, $datemodi, $modiby);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Get the ID of the item that we inserted. */
    $pitemid = $dbconn->PO_Insert_ID($planitemstable, 'xar_pitemid');

    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'itsp';
    $item['itemtype'] = 3;
    $item['itemid'] = $pitemid;
    xarModCallHooks('item', 'create', $pitemid, $item);
    /* Return the id of the newly created item to the calling process */
    return $pitemid;
}
?>