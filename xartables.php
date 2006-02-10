<?php
/**
 * Todolist table definition functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @author Original Author of file: Jim McDonald
 *
 * @subpackage Todolist Module
 */
/**
 * Todolist table definition functions
 * Return table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded.  It is loaded by xarMod__loadDbInfo().

 * @author Todolist Module development team
 * @access private
 * @return array
 */

function todolist_xartables()
{
    // Initialise table array
    $xarTables = array();

    $group_members = xarDBGetSiteTablePrefix() . '_todolist_group_members';
    $xarTables['todolist_group_members'] = $group_members;


    $groups = xarDBGetSiteTablePrefix() . '_todolist_groups';
    $xarTables['todolist_groups'] = $groups;

/* Notes are moved to comments
    $notes = xarDBGetSiteTablePrefix() . '_todolist_notes';
    $xarTables['todolist_notes'] = $notes;
*/

    $project_members = xarDBGetSiteTablePrefix() . '_todolist_project_members';
    $xarTables['todolist_project_members'] = $project_members;


    $projects = xarDBGetSiteTablePrefix() . '_todolist_projects';
    $xarTables['todolist_projects'] = $projects;


    $responsible_groups = xarDBGetSiteTablePrefix() . '_todolist_responsible_groups';
    $xarTables['todolist_responsible_groups'] = $responsible_groups;


    $responsible_persons = xarDBGetSiteTablePrefix() . '_todolist_responsible_persons';
    $xarTables['todolist_responsible_persons'] = $responsible_persons;


    $todos = xarDBGetSiteTablePrefix() . '_todolist_todos';
    $xarTables['todolist_todos'] = $todos;


    // Return the table information
    return $xarTables;
}

?>
