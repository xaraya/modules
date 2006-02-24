<?php
/**
 * Create a new course type
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses Module Development Team
 */
/**
 * Create a new coursetype
 *
 * This is a standard adminapi function to create a module item
 *
 * @author MichelV <michelv@xaraya.com>
 * @param string coursetype
 * @param string descr The desciption of this course type
 * @param string settings
 * @return int item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_createtype($args)
{
    extract($args);
    $invalid = array();
    if (!isset($coursetype) || !is_string($coursetype)) {
        $invalid[] = 'coursetype';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'createtype', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AdminCourses')) {
        return;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['courses_types'];
    /* Get next ID in table - this is required prior to any insert that
     * uses a unique ID, and ensures that the ID generation is carried
     * out in a database-portable fashion
     */
    $nextId = $dbconn->GenId($table);
    $query = "INSERT INTO $table (
              xar_tid,
              xar_type,
              xar_descr,
              xar_settings)
            VALUES (?,?,?,?)";
    $bindvars = array($nextId, (string) $coursetype,$descr,$settings);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    $tid = $dbconn->PO_Insert_ID($table, 'xar_tid');

    /* Return the id of the newly created item to the calling process */
    return $tid;
}
?>