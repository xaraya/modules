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
 * Link a plan item to a plan
 *
 * This is a standard adminapi function to create a module item
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @since 21 feb 2006
 * @return bool true on success. No id is generated
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_create_plink($args)
{
    extract($args);
    /* Argument check */
    $invalid = array();
    if (!isset($planid) || !is_numeric($planid)) {
        $invalid[] = 'planid';
    }
    if (!isset($pitemid) || !is_numeric($pitemid)) {
        $invalid[] = 'pitemid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create_plink', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('EditITSPPlan', 1, 'Plan')) {//TODO: check
        return;
    }
    $datemodi = time();
    $modiby = xarUserGetVar('uid');
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planlinkstable = $xartable['itsp_planlinks'];
    /* Get next ID in table */
    $query = "INSERT INTO $planlinkstable (
               xar_pitemid,
               xar_planid,
               xar_datemodi,
               xar_modiby)
            VALUES (?,?,?,?)";
    /* Create an array of values which correspond to the order of the
     * Question marks in the statement above.
     */
    $bindvars = array($pitemid,$planid, $datemodi,$modiby);
    $result = &$dbconn->Execute($query,$bindvars);
    /* // Hooks?
    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'itsp';
    $item['itemtype'] = 3;
    $item['itemid'] = $pitemid;
    xarModCallHooks('item', 'create', $pitemid, $item);
    */

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Return the success */
    return true;
}
?>