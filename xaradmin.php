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
// Purpose of file:  todolist administration display functions
// ----------------------------------------------------------------------

$modinfo = xarModGetInfo(xarModGetIDFromName('todolist'));

/**
 * creates a HTML-dropdownbox with the availible Users
 *
 * @param $myname            string    Name of the form-variable
 * @param $selected_names    Array    Array containing the usernr
 * @param $emty_choice        Boolean    Should an emty-entry be created? [1,0,true,false]
 * @param $multiple            Boolean    Allow multiple selects? [1,0,true,false]
 * @param $multiple            string    'all' - users from nuke_users, '' - users from nuke_todo_users
 * @return HTML containing the dropdownbox
 *
 * @Deprec Oct 2005
 * Using the Xar DD userlist
 */
function makeUserDropdownList($myname,$selected_names,$selected_project, $emty_choice, $multiple, $all) {
    global $route, $page;

    $dbconn =& xarDBGetConn();;
    $pntable =& xarDBGetTables();

    if ($all == 'all') {
       $result = $dbconn->Execute("SELECT xar_uid, xar_uname FROM $pntable[roles] ORDER BY xar_uname");
       $usercnt = $result->PO_RecordCount();
    } else {
       $todolist_project_members_column = &$pntable['todolist_project_members_column'];
       $result = $dbconn->Execute("SELECT DISTINCT $todolist_project_members_column[member_id] FROM $pntable[todolist_project_members]");
       $usercnt = $result->PO_RecordCount();
    }

    $str = "";
    if ($multiple) {
        if ($usercnt > 100) {
            $size=15;
        } elseif ($usercnt > 50) {
            $size=10;
        } elseif ($usercnt > 25) {
            $size=7;
        } elseif ($usercnt > 5) {
            $size=6;
        } elseif ($usercnt <= 5) {
            $size=$usercnt;
        }
        $myname=$myname . "[]";
        $str .= '<select multiple="multiple" name="'.$myname.'" size="'.$size.'">';
    } else  {
        $str .= '<select name="'.$myname.'" size="1">';
    }

    if ($emty_choice) {
        if ("$selected_names[0]" == "")  {
            $str .= '<option selected="selected" VALUE="">';
        } else {
            $str .= '<option value="">';
        }
    }
    if ($usercnt > 0)
    {
        for (;!$result->EOF;$result->MoveNext())
        {

            if ($all == 'all') {
                $usernr = $result->fields[0];
                $user_name = $result->fields[1];
            } else {
                $usernr = $result->fields[0];
                $user_name  = stripslashes(pnUserGetVar('name',$usernr));
                if (empty($user_name)) $user_name  = stripslashes(pnUserGetVar('uname',$usernr));
            }

            $inlist = 0;
            @reset($selected_names);
            while (@list(, $value) = @each ($selected_names)) {
                if ($value == "$usernr"){
                    $inlist = 1;
                }
            }
            if ($inlist == 1) {
                $str .= '<option selected="selected" value="'.$usernr.'">'.$user_name;
            } else {
                $str .= '<option value="'.$usernr.'">'.$user_name;
            }
            $str .= "</option>\n";
        }
    }
    $str .= '</select>';
    return $str;
}

function todolist_admin_viewprojects()
{
    $startnum = pnVarCleanFromInput('startnum');
    $startnum = 1;
    $output = new pnHTML();

    if (!pnSecAuthAction(0, 'todolist::', '::', ACCESS_EDIT)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());

    if (pnSecAuthAction(0, 'todolist::', '::', ACCESS_ADD)) {
        $output->FormStart(pnModURL('todolist', 'admin', 'createproject'));
        $output->FormHidden('authid', pnSecGenAuthKey());
        $output->TableStart(xarML('Add New Project'));

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project name')));
        $row[] = $output->FormText('project_name', '', 32, 32);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project description')));
        $row[] = $output->FormText('project_description', '', 25, 25);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project leader')));
        $row[] = $output->Text(makeUserDropdownList("project_leader",array(),"all",false,false,''));
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);
        $output->TableEnd();

        $output->Linebreak();
        $output->FormSubmit(xarML('Add'));
        $output->FormEnd();
    }

    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Projects'));

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $items = pnModAPIFunc('todolist','user','getallprojects',
                          array('startnum' => $startnum,
                          'numitems' => pnModGetVar('todolist','ITEMS_PER_PAGE')));

    $output->TableStart('',array(xarML('Id'), xarML('Project name'), xarML('Action')), 3);

    foreach ($items as $item) {
        $row = array();
        if (pnSecAuthAction(0, 'todolist::', "$item[project_name]::$item[project_id]", ACCESS_READ)) {
            $row[] = $item['project_id'];
            $row[] = $item['project_name'];

            $options = array();
            $output->SetOutputMode(_PNH_RETURNOUTPUT);
            if (pnSecAuthAction(0, 'todolist::', "$item[project_name]::$item[project_id]", ACCESS_EDIT)) {
                $options[] = $output->URL(pnModURL('todolist','admin','modifyproject',
                             array('project_id' => $item['project_id'])), xarML('Edit'));
                if (pnSecAuthAction(0, 'todolist::', "$item[project_name]::$item[project_id]", ACCESS_DELETE)) {
                    $options[] = $output->URL(pnModURL('todolist','admin','deleteproject',
                             array('project_id' => $item['project_id'])), xarML('Delete'));
                }
            }
            $options = join(' | ', $options);
            $output->SetInputMode(_PNH_VERBATIMINPUT);
            $row[] = $output->Text($options);
            $output->SetOutputMode(_PNH_KEEPOUTPUT);
            $output->TableAddRow($row,'LEFT');
            $output->SetInputMode(_PNH_PARSEINPUT);
        }
    }
    $output->TableEnd();

    $output->Pager($startnum,
                    pnModAPIFunc('todolist', 'user', 'countprojects'),
                    pnModURL('todolist','admin','viewprojects',array('startnum' => '%%')),
                    pnModGetVar('todolist', 'ITEMS_PER_PAGE'));

    return $output->GetOutput();
}


// create new group
function todolist_admin_creategroup($args)
{
    list($group_name,$group_description,$group_leader) =
        pnVarCleanFromInput('group_name','group_description','group_leader');
    extract($args);

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $result = pnModAPIFunc('todolist','admin','creategroup',
                        array('group_name' => $group_name,
                        'group_description' => $group_description,
                        'group_leader' => $group_leader));

    if ($result != false) {
        pnSessionSetVar('statusmsg', xarML('Group was created'));
    }

    pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));

    return true;
}


// This is a standard function that is called with the results of the
// form supplied by todolist_admin_modify() to update a current item
// @param 'tid' the id of the item to be updated
// @param 'name' the name of the item to be updated
// @param 'number' the number of the item to be updated
function todolist_admin_updategroup($args)
{
    list($group_id,$group_name,$group_description,$group_leader,$group_members) =
         pnVarCleanFromInput('new_group_id','new_group_name','new_group_description','new_group_leader','new_group_members');

    extract($args);

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', xarML('Load of module failed'));
        return $output->GetOutput();
    }

    if(pnModAPIFunc('todolist','admin','updategroup',
                    array('group_id' => $group_id,'group_name' => $group_name,
                    'group_description' => $group_description,'group_leader' => $group_leader,
                    'group_members' => $group_members))) {
        pnSessionSetVar('statusmsg', xarML('Updated'));
    }
    pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));

    return true;
}

function todolist_admin_deletegroup($args)
{
    list($group_id, $confirmation) =
        pnVarCleanFromInput('group_id','confirmation');

    extract($args);

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getgroup', array('group_id' => $group_id));

    if ($item == false) {
        $output->Text(xarML('No such group'));
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_DELETE)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    if (empty($confirmation)) {
        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(todolist_adminmenu());
        $output->SetInputMode(_PNH_PARSEINPUT);

        $output->Title(xarML('Delete'));

        $output->ConfirmAction(xarML('Confirm deletion'),
                               pnModURL('todolist','admin','deletegroup'),
                               xarML('Cancel deletion'),
                               pnModURL('todolist','admin','viewgroups'),
                               array('group_id' => $group_id));

        return $output->GetOutput();
    }

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    if (pnModAPIFunc('todolist','admin','deletegroup',
                     array('group_id' => $group_id))) {
        pnSessionSetVar('statusmsg', xarML('Group was deleted'));
    }

    pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));

    return true;
}

// add new user
function todolist_admin_createuser($args)
{
    list($user_id, $user_email_notify, $user_primary_project, $user_my_tasks, $user_show_icons) =
        pnVarCleanFromInput('user_id', 'user_email_notify', 'user_primary_project', 'user_my_tasks','user_show_icons');
    extract($args);

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $result = pnModAPIFunc('todolist','admin','createuser',
                        array('user_id' => $user_id,
                        'user_email_notify' => $user_email_notify,
                        'user_primary_project' => $user_primary_project,
                        'user_my_tasks' => $user_my_tasks,
                        'user_show_icons' => $user_show_icons));

    if ($result != false) {
        pnSessionSetVar('statusmsg', xarML('User was created'));
    }

    pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));

    return true;
}


function todolist_admin_modifyuser($args)
{
    list($user_id) = pnVarCleanFromInput('user_id');
    extract($args);

    $output = new pnHTML();

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getuser',array('user_id' => $user_id));

    if ($item == false) {
        $output->Text(xarML('No such user'));
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Edit User'));

    $output->FormStart(pnModURL('todolist', 'admin', 'updateuser'));
    $output->FormHidden('authid', pnSecGenAuthKey());
    $output->FormHidden('new_user_id', pnVarPrepForDisplay($user_id));
    $output->TableStart();

    // Email notify
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('User Name')));
    $row[] = $output->Text(pnVarPrepForDisplay(pnUserGetVar('uname',$item['user_id'])));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Email notify
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Email notify')));
    $row[] = $output->FormText('new_user_email_notify', pnVarPrepForDisplay($item['user_email_notify']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Primary Project
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Primary project')));
    $row[] = $output->FormText('new_user_primary_project', pnVarPrepForDisplay($item['user_primary_project']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // My Tasks
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('My tasks')));
    $row[] = $output->FormText('new_user_my_tasks', pnVarPrepForDisplay($item['user_my_tasks']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);


    // Show Icons
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show icons')));
    $row[] = $output->FormText('new_user_show_icons', pnVarPrepForDisplay($item['user_show_icons']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->TableEnd();
    $output->Linebreak(2);
    $output->FormSubmit(xarML('Update'));
    $output->FormEnd();

    return $output->GetOutput();
}

// This is a standard function that is called with the results of the
// form supplied by todolist_admin_modify() to update a current item
// @param 'tid' the id of the item to be updated
// @param 'name' the name of the item to be updated
// @param 'number' the number of the item to be updated
function todolist_admin_updateuser($args)
{
    list($user_id, $user_email_notify, $user_primary_project, $user_my_tasks, $user_show_icons) =
        pnVarCleanFromInput('new_user_id', 'new_user_email_notify', 'new_user_primary_project', 'new_user_my_tasks','new_user_show_icons');

    extract($args);

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', xarML('Load of module failed'));
        return $output->GetOutput();
    }

    if(pnModAPIFunc('todolist','admin','updateuser',
                        array('user_email_notify' => $user_email_notify,
                        'user_primary_project' => $user_primary_project,
                        'user_my_tasks' => $user_my_tasks,
                        'user_show_icons' => $user_show_icons))) {
        pnSessionSetVar('statusmsg', xarML('Updated'));
    }
    pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));

    return true;
}

function todolist_admin_deleteuser($args)
{
    list($user_id, $confirmation) =
        pnVarCleanFromInput('user_id','confirmation');

    extract($args);

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getuser', array('user_id' => $user_id));

    if ($item == false) {
        $output->Text(xarML('No such user'));
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_DELETE)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    if (empty($confirmation)) {
        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(todolist_adminmenu());
        $output->SetInputMode(_PNH_PARSEINPUT);

        $output->Title(xarML('Delete'));

        $output->ConfirmAction(xarML('Confirm deletion'),
                               pnModURL('todolist','admin','deleteuser'),
                               xarML('Cancel deletion'),
                               pnModURL('todolist','admin','viewusers'),
                               array('user_id' => $user_id));

        return $output->GetOutput();
    }

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    if (pnModAPIFunc('todolist','admin','deleteuser',
                     array('user_id' => $user_id))) {
        pnSessionSetVar('statusmsg', xarML('User was deleted'));
    }

    pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));

    return true;
}

function todolist_admin_viewusers()
{
    $startnum = pnVarCleanFromInput('startnum');
    $startnum = 1;
    $output = new pnHTML();

    if (!pnSecAuthAction(0, 'todolist::', '::', ACCESS_EDIT)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());

    if (pnSecAuthAction(0, 'todolist::', '::', ACCESS_ADD)) {
        $output->FormStart(pnModURL('todolist', 'admin', 'createuser'));
        $output->FormHidden('authid', pnSecGenAuthKey());
        $output->TableStart(xarML('Add User'));

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('User')));
        $row[] = $output->Text(makeUserDropdownList("user_id",array(),"all",false,false,'all'));
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Email notify')));
        $row[] = $output->FormText('user_email_notify', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Primary project')));
        $row[] = $output->FormText('user_primary_project', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('My tasks')));
        $row[] = $output->FormText('user_my_tasks', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show icons')));
        $row[] = $output->FormText('user_show_icons', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $output->TableEnd();

        $output->Linebreak();
        $output->FormSubmit(xarML('Add'));
        $output->FormEnd();
    }

    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Users'));

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $items = pnModAPIFunc('todolist','user','getallusers',
                          array('startnum' => $startnum,
                          'numitems' => pnModGetVar('todolist','ITEMS_PER_PAGE')));

    $output->TableStart('',array(xarML('User Name'), xarML('Email notify'), xarML('Primary project'),
    xarML('My tasks'), xarML('Show icons'), xarML('Action')), 6);

    foreach ($items as $item) {
        $row = array();
        if (pnSecAuthAction(0, 'todolist::', "::$item[user_id]", ACCESS_READ)) {
            $row[] = pnUserGetVar('uname',$item['user_id']);
            $row[] = $item['user_email_notify'];
            $row[] = $item['user_primary_project'];
            $row[] = $item['user_my_tasks'];
            $row[] = $item['user_show_icons'];

            $options = array();
            $output->SetOutputMode(_PNH_RETURNOUTPUT);
            if (pnSecAuthAction(0, 'todolist::', "::$item[user_id]", ACCESS_EDIT)) {
                $options[] = $output->URL(pnModURL('todolist','admin','modifyuser',
                                                   array('user_id' => $item['user_id'])), xarML('Edit'));
                if (pnSecAuthAction(0, 'todolist::', "::$item[user_id]", ACCESS_DELETE)) {
                    $options[] = $output->URL(pnModURL('todolist','admin','deleteuser',
                                                       array('user_id' => $item['user_id'])), xarML('Delete'));
                }
            }
            $options = join(' | ', $options);
            $output->SetInputMode(_PNH_VERBATIMINPUT);
            $row[] = $output->Text($options);
            $output->SetOutputMode(_PNH_KEEPOUTPUT);
            $output->TableAddRow($row,'LEFT');
            $output->SetInputMode(_PNH_PARSEINPUT);
        }
    }

    $output->TableEnd();

    $output->Pager($startnum,
                    pnModAPIFunc('todolist', 'user', 'countusers'),
                    pnModURL('todolist','admin','viewusers',array('startnum' => '%%')),
                    pnModGetVar('todolist', 'ITEMS_PER_PAGE'));

    return $output->GetOutput();
}

?>