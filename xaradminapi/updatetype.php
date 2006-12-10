<?php
/**
 * Update an course type
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Update an course type
 *
 * @author the Courses module development team
 * @param  $args ['tid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['descr'] the new number of the item
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_updatetype($args)
{
    extract($args);
    $invalid = array();
    if (!isset($tid) || !is_numeric($tid)) {
        $invalid[] = 'item ID';
    }
    if (!isset($coursetype) || !is_string($coursetype)) {
        $invalid[] = 'coursetype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updatetype', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $item = xarModAPIFunc('courses',
        'user',
        'gettype',
        array('tid' => $tid));
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('AdminCourses')) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you
     * are getting - $table and $column don't cut it in more complex
     * modules
     */
    $typestable = $xartable['courses_types'];
    /* Update the item - the formatting here is not mandatory, but it does
     * make the SQL statement relatively easy to read. Also, separating
     * out the sql statement from the Execute() command allows for simpler
     * debug operation if it is ever needed
     */
    $query = "UPDATE $typestable
            SET xar_type =?,
                xar_descr =?,
                xar_settings =?
            WHERE xar_tid = ?";
    $bindvars = array($coursetype, $descr, $settings, $tid);
    $result = &$dbconn->Execute($query,$bindvars);
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    /* Let the calling process know that we have finished successfully */
    return true;
}
?>