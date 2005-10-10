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
    
    $group_memberstable = xarDBGetSiteTablePrefix() . '_todolist_group_members';
    $xarTables['todolist_group_members'] = $todolist_group_members;


    $groupstable = xarDBGetSiteTablePrefix() . '_todolist_groups';
    $xarTables['todolist_groups'] = $todolist_groups;

/* Notes are moved to comments
    $notestable = xarDBGetSiteTablePrefix() . '_todolist_notes';
    $xarTables['todolist_notes'] = $todolist_notes;
*/

    $memberstable = xarDBGetSiteTablePrefix() . '_todolist_project_members';
    $xarTables['todolist_project_members'] = $todolist_project_members;


    $projectstable = xarDBGetSiteTablePrefix() . '_todolist_projects';
    $xarTables['todolist_projects'] = $todolist_projects;


    $responsible_groupstable = xarDBGetSiteTablePrefix() . '_todolist_responsible_groups';
    $xarTables['todolist_responsible_groups'] = $todolist_responsible_groups;


    $responsible_personstable = xarDBGetSiteTablePrefix() . '_todolist_responsible_persons';
    $xarTables['todolist_responsible_persons'] = $todolist_responsible_persons;


    $todostable = xarDBGetSiteTablePrefix() . '_todolist_todos';
    $xarTables['todolist_todos'] = $todolist_todos;


    // Return the table information
    return $xarTables;
}

?>
