<?php
/**
 * Utility function to get DD item for type
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Gets items of a DynamicData object 'type'
 *
 * @author Brian McGilligan
 * @param $args['itemtype'] - Item type
 * @returns list of items of the item type
 */
function courses_userapi_gettype($args)
{
    extract($args);

    if (!isset($tid) || !is_numeric($tid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'user', 'gettype', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['courses_types'];
    /* Get item */
    $query = "SELECT xar_tid,
                     xar_type
              FROM $table
              WHERE xar_tid = ?";
    $result = &$dbconn->Execute($query,array($tid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
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
    list($tid, $coursetype) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Security check
     */
    if (!xarSecurityCheck('ViewCourses')) {
        return;
    }
    /* Create the item array */
    $item = array('tid'   => $tid,
                  'coursetype'  => $coursetype);
    /* Return the item array */
    return $item;
}
?>
