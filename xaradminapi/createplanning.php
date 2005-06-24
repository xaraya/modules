<?php
/**
 * File: $Id:
 *
 * Create a new Course item
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage courses
 * @author Courses module development team
 */
/**
 * create a new course planning
 *
 * @author the Courses module development team
 * @param  $args ['courseid'] ID of the course
 * @param  $args ['number'] number of the course
 * @returns int
 * @return planning ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function courses_adminapi_createplanning($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other
    // places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    extract($args);
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
	/*
    $invalid = array();
    if (!isset($name) || !is_string($name)) {
        $invalid[] = 'name';
    }
    if (!isset($number) || !is_string($number)) {
        $invalid[] = 'number';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'adminapi', 'createplanning', 'Courses');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    } 
	*/
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddCourses', 1, 'Item', "All:All:All")) {
        return;
    }
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $planningtable = $xartable['courses_planning'];
    // Get next ID in table
    $nextId = $dbconn->GenId($planningtable);
    // Add item - the formatting here is not mandatory, but it does make
    // the SQL statement relatively easy to read.  Also, separating out
    // the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $query = "INSERT INTO $planningtable (
	                       xar_planningid,
	                       xar_courseid,
                           xar_courseyear,
                           xar_credits,
                           xar_creditsmin,
						   xar_creditsmax,
						   xar_startdate,
						   xar_enddate,
						   xar_prerequisites,
						   xar_aim,
						   xar_method,
						   xar_longdesc,
						   xar_costs,
						   xar_committee,
						   xar_coordinators,
						   xar_lecturers,
						   xar_location,
						   xar_material,
						   xar_info,
						   xar_program,
                           xar_hideplanning)
			  VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
			
    $bindvars = array((int)$nextId, $courseid, $year, $credits, $creditsmin, $creditsmax, $startdate, $enddate, $prerequisites, $aim, $method, $longdesc,
	 $costs, $committee, $coordinators, $lecturers, $location, $material, $info, $program, $hideplanning);
    $result = &$dbconn->Execute($query, $bindvars);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $planningid = $dbconn->PO_Insert_ID($planningtable, 'xar_planningid');
    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'courseid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
    // TODO: evaluate
    // xarModCallHooks('item', 'create', $courseid, 'courseid');
    $item = $args;
    $item['module'] = 'courses';
    $item['itemid'] = $planningid;
    xarModCallHooks('item', 'create', $planningid, $item);
    // Return the id of the newly created item to the calling process
    return $planningid;
}

?>
