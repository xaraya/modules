<?php
/**
 * Get a specific maxer
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
 * Get a specific maxer
 *
 * Standard function of a module to retrieve a specific item
 *
 * @author the Maxercalls module development team
 * @param  $args ['maxerid'] id of maxercalls item to get
 * @returns array
 * @return item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function maxercalls_userapi_getmaxer($args)
{
    extract($args);
    /* Argument check
     */
    if (!isset($maxerid) || !is_numeric($maxerid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'getmaxer', 'Maxercalls');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get database setup
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $maxerstable = $xartable['maxercalls_maxers'];
    $query = "SELECT xar_personid,
               xar_ric,
               xar_maxernumber,
               xar_function,
               xar_program,
               xar_maxerstatus,
               xar_remark
              FROM $maxerstable
              WHERE xar_maxerid = ?";
    $result = &$dbconn->Execute($query,array($maxerid));
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
    list($personid,$ric,$maxernumber,$function,
       $program,$maxerstatus,$remark) = $result->fields;
    $result->Close();
    /* Security check
     */
    if (!xarSecurityCheck('ReadMaxercalls', 1, 'Maxer', "All:All:All")) {
        return;
    }
    /* Create the item array */
    $item = array('maxerid'     => $maxerid,
                  'personid'    => $personid,
                  'ric'         => $ric,
                  'maxernumber' => $maxernumber,
                  'function'    => $function,
                  'program'     => $program,
                  'maxerstatus' => $maxerstatus,
                  'remark'      => $remark);
    /* Return the item array */
    return $item;
}
?>