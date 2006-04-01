<?php
/**
 * Create a new DD courses item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * create a new DD course item
 *
 * @author the course module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @return int item ID on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 * @deprecated 2005
 */
function courses_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($number) || !is_numeric($number)) {
        $invalid[] = 'number';
    }

     if (!isset($hours) || !is_numeric($hours)) {
        $invalid[] = 'hours';
    }

     if (!isset($ceu) || !is_numeric($ceu)) {
        $invalid[] = 'ceu';
    }

 if (empty($startdate) || !is_string($startdate)) {
        $invalid['startdate'] = 1;
        $startdate = '';
    }

     if (empty($enddate) || !is_string($enddate)) {
        $invalid['enddate'] = 1;
        $enddate = '';
    }


    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddCourses', 1, 'Item', "$name:All:All")) {
        return;
    }
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $coursestable = $xartable['courses'];
    // Get next ID in table - this is required prior to any insert that
    // uses a unique ID, and ensures that the ID generation is carried
    // out in a database-portable fashion
    $nextId = $dbconn->GenId($coursestable);
    // Add item - the formatting here is not mandatory, but it does make
    // the SQL statement relatively easy to read.  Also, separating out
    // the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $query = "INSERT INTO $coursestable (
              xar_courseid,
              xar_name,
              xar_number,
              xar_hours,
              xar_ceu,
              xar_startdate,
              xar_enddate,
              xar_shortdesc,
              xar_longdesc)
            VALUES (?,?,?,?,?,?,?,?,?)";
    $bindvars = array($nextId, $name, $number, $hours, $ceu, $startdate, $enddate, $shortdesc, $longdesc);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $courseid = $dbconn->PO_Insert_ID($coursestable, 'xar_courseid');
    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'exid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
    // TODO: evaluate
    // xarModCallHooks('item', 'create', $exid, 'exid');
    $item = $args;
    $item['module'] = 'courses';
    $item['itemid'] = $courseid;
    xarModCallHooks('item', 'create', $courseid, $item);
    // Return the id of the newly created item to the calling process
    return $courseid;
}

?>
