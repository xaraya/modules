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
 * @param itemtype
 * @author MichelV <michelv@xarayahosting.nl>
 * @return integer number of items held by this module
 * @throws DATABASE_ERROR
 */
function itsp_userapi_countitems($args)
{
    extract ($args);
    if (!isset($itemtype) || !is_numeric($itemtype)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'countitems', 'ITSP');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    //Switch for the itemtypes
    switch ($itemtype) {
        case '1':
        $table = $xartable['itsp_plans'];
        case '2':
        $table = $xartable['itsp_itsp'];
    }
    $query = "SELECT COUNT(1)
              FROM $table";
    /* If there are no variables you can pass in an empty array for bind variables
     * or no parameter.
     */
    $result = &$dbconn->Execute($query,array());
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Obtain the number of items */
    list($numitems) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>