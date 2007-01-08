<?php
/**
 * Delete a student item
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author MichelV.
 */
/**
 * delete a student
 *
 * @author the Courses module development team
 * @param  $args ['sid'] ID of the student item
 * @returns bool
 * @return true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_deleteparticipant($args)
{
    extract($args);
    if (!xarVarFetch('sid', 'id', $sid)) return;

    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($sid) || !is_numeric($sid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'deleteparticipant', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // get item
    $item = xarModAPIFunc('courses',
        'user',
        'getparticipant',
        array('sid' => $sid));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditCourses', 1, 'Course', "All:All:All")) { //TODO rewrite
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $studentstable = $xartable['courses_students'];
    $query = "DELETE
              FROM $studentstable
              WHERE xar_sid = ?";
    $result = &$dbconn->Execute($query, array((int)$sid));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let the calling process know that we have finished successfully
    return true;
}

?>
