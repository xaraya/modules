<?php
/**
 * Courses table definitions function
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */

/**
 * Return courses table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().
 *
 * @access private
 * @return array
 */
function courses_xartables()
{
    // Initialise table array
    $xarTables = array();
    // Get the name for the course table.  This is not necessary
    // but helps in the following statements and keeps them readable

    $courses = xarDBGetSiteTablePrefix() . '_courses';
    // Set the table name
    $xarTables['courses'] = $courses;

    $courses_planning = xarDBGetSiteTablePrefix() . '_courses_planning';
    // Set the table name
    $xarTables['courses_planning'] = $courses_planning;

    $courses_students = xarDBGetSiteTablePrefix() . '_courses_students';
    // Set the table name
    $xarTables['courses_students'] = $courses_students;

    $courses_teachers = xarDBGetSiteTablePrefix() . '_courses_teachers';
    // Set the table name
    $xarTables['courses_teachers'] = $courses_teachers;

    $courses_levels = xarDBGetSiteTablePrefix() . '_courses_levels';
        // Set the table name
    $xarTables['courses_levels'] = $courses_levels;

    $courses_types = xarDBGetSiteTablePrefix() . '_courses_types';
        // Set the table name
    $xarTables['courses_types'] = $courses_types;

    $courses_studstatus = xarDBGetSiteTablePrefix() . '_courses_studstatus';
    // Set the table name
    $xarTables['courses_studstatus'] = $courses_studstatus;

    $courses_years = xarDBGetSiteTablePrefix() . '_courses_years';
        // Set the table name
    $xarTables['courses_years'] = $courses_years;
    // Return the table information
    return $xarTables;
}

?>
