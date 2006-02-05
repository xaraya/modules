<?php
/**
 * Create a new course type
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Create a new coursetype
 *
 * This is a standard adminapi function to create a module item
 *
 * @author the Example module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return example item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
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
    /* Add item - the formatting here is not mandatory, but it does make
     * the SQL statement relatively easy to read. Also, separating out
     * the sql statement from the Execute() command allows for simpler
     * debug operation if it is ever needed
     */
    $query = "INSERT INTO $table (
              xar_tid,
              xar_type)
            VALUES (?,?)";
    $bindvars = array($nextId, (string) $coursetype);
    $result = &$dbconn->Execute($query,$bindvars);

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    $tid = $dbconn->PO_Insert_ID($table, 'xar_tid');

    /* Let any hooks know that we have created a new item. As this is a
     * create hook we're passing 'exid' as the extra info, which is the
     * argument that all of the other functions use to reference this
     * item
     * TODO: evaluate
     * xarModCallHooks('item', 'create', $exid, 'exid');

    $item = $args;
    $item['module'] = 'courses';
    $item['itemid'] = $tid;
    xarModCallHooks('item', 'create', $tid, $item);     */
    /* Return the id of the newly created item to the calling process */
    return $tid;
}
?>