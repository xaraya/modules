<?php
/**
 * Get all maxers
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls Module Development Team
 */
/**
 * Get all maxers
 *
 * @author the Maxercalls module development team
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function maxercalls_userapi_getallmaxers($args)
{
    extract($args);
    /* Optional arguments.*/
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     * Note : since we have several arguments we want to check here, we'll
     * report all those that are invalid at the same time...
     */
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getallmaxers', 'Maxercalls');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $items = array();
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('ViewMaxercalls',1,'Maxer')) return;
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $maxerstable = $xartable['maxercalls_maxers'];
    $query = "SELECT xar_maxerid,
                     xar_personid,
                     xar_ric,
                     xar_maxernumber,
                     xar_function,
                     xar_program,
                     xar_maxerstatus,
                     xar_remark
             FROM $maxerstable
             ORDER BY xar_ric";
    /* SelectLimit also supports bind variable, they get to be put in
     * as the last parameter in the function below. In this case we have no
     * bind variables, so we left the parameter out. We could have passed in an
     * empty array though.
     */
    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($maxerid, $personid,$ric,$maxernumber,$function,
       $program,$maxerstatus,$remark) = $result->fields;
        if (xarSecurityCheck('ViewMaxercalls', 0, 'Maxer', "All:All:All")) {
            $items[] = array('maxerid'     => $maxerid,
                             'personid'    => $personid,
                             'ric'         => $ric,
                             'maxernumber' => $maxernumber,
                             'function'    => $function,
                             'program'     => $program,
                             'maxerstatus' => $maxerstatus,
                             'remark'      => $remark);
        }
    }
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the items */
    return $items;
}
?>