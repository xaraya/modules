<?php // $Id$
// ----------------------------------------------------------------------
// POST-NUKE Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
// ----------------------------------------------------------------------
// Based on:
// PHP-NUKE Web Portal System - http://phpnuke.org/
// Thatware - http://thatware.org/
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Jim McDonald
// Purpose of file:  todolist administration API
// ----------------------------------------------------------------------

function todolist_adminapi_creategroup($args)
{
    extract($args);
    
   if ((!isset($group_name)) && (!isset($group_description)) && (!isset($group_leader))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_groups_column = &$pntable['todolist_groups_column'];
    $newgid = $dbconn->GenId("$pntable[todolist_groups]");
    $result = $dbconn->Execute("INSERT INTO $pntable[todolist_groups] VALUES
                    ($newgid,'$group_name','$group_description',$group_leader)");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }
    $newgid = $dbconn->PO_Insert_ID("$pntable[todolist_groups]","$todolist_groups_column[id]");

    $result = $dbconn->Execute("INSERT INTO $pntable[todolist_group_members] VALUES
                    ($newgid,$group_leader)");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }
    return true;
}


function todolist_adminapi_updategroup($args)
{
    extract($args);
    
   if ((!isset($group_id)) && (!isset($group_name)) && (!isset($group_description)) && (!isset($groupt_leader))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_groups_column = &$pntable['todolist_groups_column'];
    $query = "UPDATE $pntable[todolist_groups] SET
                      $todolist_groups_column[group_name]='$new_group_name',
                      $todolist_groups_column[description]='$new_group_description',
                      $todolist_groups_column[group_leader]=$new_group_leader
                      WHERE $todolist_groups_column[id]=$group_id";
    $result = $dbconn->Execute($query);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Update error occured'));
        return false;
    }
    // update group-members... Is there a more elegant way to do this?
    // do we have to delete the tasks where someone is assigned who is no longer
    // member of the group?
    $todolist_group_members_column = &$pntable['todolist_group_members_column'];
    $query = "DELETE from $pntable[todolist_group_members]
                      WHERE $todolist_group_members_column[group_id]=$group_id";
    $result = $dbconn->Execute($query);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    if (sizeof($new_group_members) > 0) {
        $query="INSERT INTO $pntable[todolist_group_members] VALUES ";
        
        while ($member_id=array_pop($new_group_members)){
            $query .= "($group_id, $member_id)";
            if (sizeof($new_group_members) > 0)
                $query .= ',';
        }
    }
    $result = $dbconn->Execute("$query");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }

    pnSessionSetVar('errormsg', xarML('Group was updated'));
    return true;
}

// Deletes a whole group. A mail-notify is not generated!
// @param $group_id    int    the primary key of the group
function todolist_adminapi_deletegroup($args)
{
    // TODO: We have to make user only ADMIN and group-leader can do that!
    extract($args);
    
    if (!isset($group_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_groups_column = &$pntable['todolist_groups_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_groups]
        WHERE $todolist_groups_column[id]=$group_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    $todolist_group_members_column = &$pntable['todolist_group_members_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_group_members]
        WHERE $todolist_group_members_column[group_id]=$group_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    $todolist_responsible_groups_column = &$pntable['todolist_responsible_groups_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_responsible_groups]
        WHERE $todolist_responsible_groups_column[group_id]=$group_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    return true;
}

function todolist_adminapi_createproject($args)
{
    extract($args);
    
   if ((!isset($project_name)) && (!isset($project_description)) && (!isset($project_leader))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_projects_column = &$pntable['todolist_projects_column'];
    $newpid = $dbconn->GenId("$pntable[todolist_projects]");
    $result = $dbconn->Execute("INSERT INTO $pntable[todolist_projects] VALUES
                    ($newpid,'$project_name','$project_description',$project_leader)");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }
    $newpid = $dbconn->PO_Insert_ID("$pntable[todolist_projects]","$todolist_projects_column[id]");
    $result = $dbconn->Execute("INSERT INTO $pntable[todolist_project_members] VALUES
                    ($newpid,$project_leader)");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }
    return true;
}


function todolist_adminapi_updateproject($args)
{
    extract($args);
    
   if ((!isset($project_id)) && (!isset($project_name)) && (!isset($project_description)) && (!isset($project_leader))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_projects_column = &$pntable['todolist_projects_column'];
    $query = "UPDATE $pntable[todolist_projects] SET
                     $todolist_projects_column[project_name]='$project_name',
                     $todolist_projects_column[description]='$project_description',
                     $todolist_projects_column[project_leader]=$project_leader
                     WHERE $todolist_projects_column[id] = $project_id";
    $result = $dbconn->Execute($query);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Update error occured'));
        return false;
    }
    // update project-members... Is there a more elegant way to do this?
    // do we have to delete the tasks where someone is assigned who is no longer
    // member of the project?
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $query = "DELETE FROM $pntable[todolist_project_members]
              WHERE $todolist_project_members_column[project_id] = $project_id";
    $result = $dbconn->Execute($query);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }
    if (sizeof($project_members) > 0) {
        $query="INSERT INTO $pntable[todolist_project_members] VALUES ";
         
        while ($member_id=array_pop($project_members)){
            $query .= "($project_id, $member_id)";
            if (sizeof($project_members) > 0)
                $query .= ',';
        }
    }
    $result = $dbconn->Execute("$query");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }
  
    pnSessionSetVar('errormsg', xarML('Project was updated'));
    return true;
}

// Deletes a whole project. A mail-notify is not generated!
// @param $project_id    int    the primary key of the project
function todolist_adminapi_deleteproject($args)
{
    // TODO: We have to make user only ADMIN and Project-Admins can do that!
    extract($args);
    
    if (!isset($project_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $todolist_projects_column = &$pntable['todolist_projects_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_projects] WHERE 
                    $todolist_projects_column[id] = ". $project_id);
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_project_members]
              WHERE $todolist_project_members_column[project_id]=$project_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    // get the todo_ids that have notes attached and construct a query
    $todolist_todos_column = &$pntable['todolist_todos_column'];
    $todolist_notes_column = &$pntable['todolist_notes_column'];
    $result = $dbconn->Execute("SELECT DISTINCT $todolist_notes_column[todo_id]
              FROM $pntable[todolist_todos], $pntable[todolist_notes]
              WHERE $todolist_todos_column[todo_id]=$todolist_notes_column[todo_id]
              AND $todolist_todos_column[project_id]=$project_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Select error occured'));
        return false;
    }
    for (;!$result->EOF;$result->MoveNext()){
        $notes[] = $result->fields[0];
    }
    if (sizeof($notes) > 0) {
        $todolist_notes_column = &$pntable['todolist_notes_column'];
        $query="DELETE from $pntable[todolist_notes] WHERE $todolist_notes_column[todo_id] in (";
        while ($neu=array_pop($notes)){
             $query .= $neu;
             if (sizeof($notes) > 0) {
                 $query .= ',';
             } else {
                 $query .= ')';
             }
        }
        $result = $dbconn->Execute("$query");
        if ($result === false) {
            pnSessionSetVar('errormsg', xarML('Delete error occured'));
           return false;
        }
        $todolist_todos_column = &$pntable['todolist_todos_column'];
        $result = $dbconn->Execute("DELETE FROM $pntable[todolist_todos]
                  WHERE $todolist_todos_column[project_id]=$project_id");
        if ($result === false) {
            pnSessionSetVar('errormsg', xarML('Delete error occured'));
            return false;
        }
    }
    return true;
}

function todolist_adminapi_createuser($args)
{
    extract($args);
    
    if ((!isset($user_id)) && (!isset($user_email_notify)) && (!isset($user_primary_project)) &&
        (!isset($user_my_tasks)) && (!isset($user_show_icons))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }
    //HTML-Forms submit nothing if a checkbox isn't checked... :-(
    if (!isset($user_email_notify)) { $user_email_notify = 0; }
    if (!isset($user_primary_project)) { $user_primary_project = 0; }
    if (!isset($user_my_tasks)) { $user_my_tasks = 0; }
    if (!isset($user_show_icons)) { $user_show_icons = 0; }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_ADD)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $userpref = $user_email_notify.';'.$user_primary_project.';'.$user_my_tasks.';'.$user_show_icons;
    xarModSetUserVar('todolist','userpref',$userpref,$user_id);

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    // Every user is member of project 1 (default)
    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $result = $dbconn->Execute("INSERT INTO $pntable[todolist_project_members] VALUES (1,$user_id)");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Insert error occured'));
        return false;
    }

    return true;
}

function todolist_adminapi_updateuser($args)
{
    extract($args);
    
    if ((!isset($user_id)) && (!isset($user_email_notify)) && (!isset($user_primary_project)) &&
        (!isset($user_my_tasks)) && (!isset($user_show_icons))) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }
    //HTML-Forms submit nothing if a checkbox isn't checked... :-(
    if (!isset($user_email_notify)) { $user_email_notify = 0; }
    if (!isset($user_primary_project)) { $user_primary_project = 0; }
    if (!isset($user_my_tasks)) { $user_my_tasks = 0; }
    if (!isset($user_show_icons)) { $user_show_icons = 0; }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    $userpref = $new_email_notify.';'.$new_primary_project.';'.$new_my_tasks.';'.$showicons;
    xarModSetUserVar('todolist','userpref',$userpref,$user_id);

    pnSessionSetVar('errormsg', xarML('User was updated'));
    return true;
}

// Deletes a user.
// @param $user_id    int    the primary key of the group
function todolist_adminapi_deleteuser($args)
{
    // TODO: We have to make user only ADMIN and group-leader can do that!
    extract($args);
    
    if (!isset($user_id)) {
        pnSessionSetVar('errormsg', xarML('Error in API arguments'));
        return false;
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_DELETE)) {
        pnSessionSetVar('errormsg', xarML('Not authorised to access Todolist module'));
        return false;
    }

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    xarModDelUserVar('todolist','userpref',$user_id);

    $todolist_responsible_persons_column = &$pntable['todolist_responsible_persons_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_responsible_persons]
           WHERE $todolist_responsible_persons_column[user_id]=$user_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    $todolist_group_members_column = &$pntable['todolist_group_members_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_group_members]
           WHERE $todolist_group_members_column[member_id]=$user_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    $todolist_project_members_column = &$pntable['todolist_project_members_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_project_members]
           WHERE $todolist_project_members_column[member_id]=$user_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    $todolist_notes_column = &$pntable['todolist_notes_column'];
    $result = $dbconn->Execute("DELETE FROM $pntable[todolist_notes]
           WHERE $todolist_notes_column[usernr]=$user_id");
    if ($result === false) {
        pnSessionSetVar('errormsg', xarML('Delete error occured'));
        return false;
    }

    return true;
}

?>