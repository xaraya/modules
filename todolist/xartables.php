<?php // $Id$
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  Table information for template module
// ----------------------------------------------------------------------

/**
 * This function is called internally by the core whenever the module is
 * loaded.  It adds in the information
 */
function todolist_xartables()
{
    // Initialise table array
    $pntable = array();

    // Set the column names.  Note that the array has been formatted
    // on-screen to be very easy to read by a user.

    $todolist_group_members = pnConfigGetVar('prefix') . '_todolist_group_members';
    $pntable['todolist_group_members'] = $todolist_group_members;
    $pntable['todolist_group_members_column'] = array(
        'group_id'    => $todolist_group_members . '.pn_group_id',
        'member_id' => $todolist_group_members . '.pn_member_id');

    $todolist_groups = pnConfigGetVar('prefix') . '_todolist_groups';
    $pntable['todolist_groups'] = $todolist_groups;
    $pntable['todolist_groups_column'] = array(
        'id'    => $todolist_groups . '.pn_id',
        'group_name'    => $todolist_groups . '.pn_group_name',
        'description'    => $todolist_groups . '.pn_description',
        'group_leader' => $todolist_groups . '.pn_group_leader');

    $todolist_notes = pnConfigGetVar('prefix') . '_todolist_notes';
    $pntable['todolist_notes'] = $todolist_notes;
    $pntable['todolist_notes_column'] = array(
        'todo_id'    => $todolist_notes . '.pn_todo_id',
        'note_id'    => $todolist_notes . '.pn_note_id',
        'text'    => $todolist_notes . '.pn_text',
        'usernr'    => $todolist_notes . '.pn_usernr',
        'date' => $todolist_notes . '.pn_date');

    $todolist_project_members = pnConfigGetVar('prefix') . '_todolist_project_members';
    $pntable['todolist_project_members'] = $todolist_project_members;
    $pntable['todolist_project_members_column'] = array(
        'project_id'    => $todolist_project_members . '.pn_project_id',
        'member_id' => $todolist_project_members . '.pn_member_id');

    $todolist_projects = pnConfigGetVar('prefix') . '_todolist_projects';
    $pntable['todolist_projects'] = $todolist_projects;
    $pntable['todolist_projects_column'] = array(
        'id'    => $todolist_projects . '.pn_id',
        'project_name'    => $todolist_projects . '.pn_project_name',
        'description'    => $todolist_projects . '.pn_description',
        'project_leader' => $todolist_projects . '.pn_project_leader');

    $todolist_responsible_groups = pnConfigGetVar('prefix') . '_todolist_responsible_groups';
    $pntable['todolist_responsible_groups'] = $todolist_responsible_groups;
    $pntable['todolist_responsible_groups_column'] = array(
        'todo_id'    => $todolist_responsible_groups . '.pn_todo_id',
        'group_id' => $todolist_responsible_groups . '.pn_group_id');

    $todolist_responsible_persons = pnConfigGetVar('prefix') . '_todolist_responsible_persons';
    $pntable['todolist_responsible_persons'] = $todolist_responsible_persons;
    $pntable['todolist_responsible_persons_column'] = array(
        'todo_id'    => $todolist_responsible_persons . '.pn_todo_id',
        'user_id' => $todolist_responsible_persons . '.pn_user_id');

    $todolist_todos = pnConfigGetVar('prefix') . '_todolist_todos';
    $pntable['todolist_todos'] = $todolist_todos;
    $pntable['todolist_todos_column'] = array(
        'todo_id'    => $todolist_todos . '.pn_todo_id',
        'project_id'    => $todolist_todos . '.pn_project_id',
        'todo_text'    => $todolist_todos . '.pn_todo_text',
        'todo_priority'    => $todolist_todos . '.pn_todo_priority',
        'percentage_completed'    => $todolist_todos . '.pn_percentage_completed',
        'created_by'    => $todolist_todos . '.pn_created_by',
        'due_date'    => $todolist_todos . '.pn_due_date',
        'date_created'    => $todolist_todos . '.pn_date_created',
        'date_changed'    => $todolist_todos . '.pn_date_changed',
        'changed_by'    => $todolist_todos . '.pn_changed_by',
        'status' => $todolist_todos . '.pn_status');

    // Return the table information
    return $pntable;
}

?>
