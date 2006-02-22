<?php
/**
 * Utility function to count the number of items held by this module
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
 * Utility function to count the number of items held by this module
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @param int pitemid The plan item ID
 * @return integer number of credits for this plan item
 * @throws BAD_PARAM DATABASE_ERROR
 */
function itsp_userapi_countcredits($args)
{
    extract ($args);
    if (!isset($pitemid) || !is_numeric($pitemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'countcredits', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // we can only count directly in our own courses table
    $table = $xartable['itsp_itsp_courses'];

    $query = "SELECT SUM(xar_icoursecredits)
              FROM $table
              WHERE xar_pitemid = ?";
    $result = &$dbconn->Execute($query,array($pitemid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Obtain the number of items */
    list($credits) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the number of items */
    return $credits;
}
?>