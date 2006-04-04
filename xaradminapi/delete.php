<?php
/**
 * Delete an itsp item
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
 * Delete a plan
 *
 * Standard function to delete a module item
 *
 * @author the ITSP module development team
 * @param  $args ['planid'] ID of the item
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_delete($args)
{
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($planid) || !is_numeric($planid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'delete', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get the plan: check
     */
    $item = xarModAPIFunc('itsp',
        'user',
        'get_plan',
        array('planid' => $planid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    // Security check
    if (!xarSecurityCheck('DeleteITSPPlan', 1, 'Plan', "$planid:All")) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $planstable = $xartable['itsp_plans'];
    /* Delete the item - the formatting here is not mandatory, but it does
     * make the SQL statement relatively easy to read.  Also, separating
     * out the sql statement from the Execute() command allows for simpler
     * debug operation if it is ever needed
     */
    $query = "DELETE FROM $planstable WHERE xar_planid = ?";

    /* The bind variable $exid is directly put in as a parameter. */
    $result = &$dbconn->Execute($query,array($planid));

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have deleted an item.  As this is a
     * delete hook we're not passing any extra info
     * xarModCallHooks('item', 'delete', $exid, '');
     */
    $item['module'] = 'itsp';
    $item['itemtype'] = 1;
    $item['itemid'] = $planid;
    xarModCallHooks('item', 'delete', $planid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>