<?php
/**
 * Courses table definitions function
 *
 * @package modules
 * @copyright (C) 2005-2008 The Digital Development Foundation
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
    // Add other tables
    $basename = 'courses';
    foreach(array('planning', 'students', 'teachers', 'levels', 'types', 'studstatus','years') as $table) {
        // Set the table name.
        $xarTables[$basename . '_' . $table] = xarDBGetSiteTablePrefix() . '_' . $basename . '_' . $table;
    }

    // Return the table information
    return $xarTables;
}

?>
