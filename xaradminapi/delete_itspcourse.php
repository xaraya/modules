<?php
/**
 * Delete an itsp item
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
 * Delete a linked course
 *
 * Standard function to delete a module item
 *
 * @author the ITSP module development team
 * @param  $args ['planid'] ID of the item
 * @return bool true on success, false on failure
 * @since 30 August 2006
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function itsp_adminapi_delete_itspcourse($args)
{
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($icourseid) || !is_numeric($icourseid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'adminapi', 'delete_itspcourse', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get the plan: check
     */
    $item = xarModAPIFunc('itsp',
        'user',
        'get_itspcourse',
        array('icourseid' => $icourseid));
    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check
    if (!xarSecurityCheck('DeleteITSPPlan', 1, 'Plan', "$planid:All")) {
        return;
    }*/
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $icoursestable = $xartable['itsp_itsp_courses'];
    /* Delete the item */
    $query = "DELETE FROM $icoursestable WHERE xar_icourseid = ?";

    /* The bind variable $courselinkid is directly put in as a parameter. */
    $result = &$dbconn->Execute($query,array($icourseid));

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Let any hooks know that we have deleted an item.  As this is a
     * delete hook we're not passing any extra info
     * xarModCallHooks('item', 'delete', $exid, '');
     */
    $item['module'] = 'itsp';
    $item['itemtype'] = 5;
    $item['itemid'] = $icourseid;
    xarModCallHooks('item', 'delete', $icourseid, $item);

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>