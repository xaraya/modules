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

$modinfo = pnModGetInfo(pnModGetIDFromName('todolist'));

/**
 * creates a HTML-dropdownbox with the availible Users
 *
 * @param $myname            string    Name of the form-variable
 * @param $selected_names    Array    Array containing the usernr
 * @param $emty_choice        Boolean    Should an emty-entry be created? [1,0,true,false]
 * @param $multiple            Boolean    Allow multiple selects? [1,0,true,false]
 * @param $multiple            string    'all' - users from nuke_users, '' - users from nuke_todo_users
 * @return HTML containing the dropdownbox
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

function todolist_admin_main()
{
    $output = new pnHTML();

    if (!pnSecAuthAction(0, 'todolist::Item', '::', ACCESS_EDIT)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    return $output->GetOutput();
}

// Main administration menu
function todolist_adminmenu()
{
   
    $output = new pnHTML();

    $authid = pnSecGenAuthKey();

    if(!(pnSecAuthAction(0, 'todolist::', '::', ACCESS_EDIT))) {
	$output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(pnGetStatusMsg());
    $output->Linebreak();

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $output->TableStart(xarML('Todolist administration'), '', 1);
    $output->TableRowStart();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewusers'),xarML('Users')); 
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewgroups'),xarML('Groups')); 
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewprojects'),xarML('Projects')); 
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','modifyconfig'),xarML('Edit Configuration')); 
    $output->TableColEnd();
    $output->TableRowEnd();
    $output->TableEnd();
    $output->SetInputMode(_PNH_PARSEINPUT);
    return $output->GetOutput();
}

// create new project
function todolist_admin_createproject($args)
{
    list($project_name,$project_description,$project_leader) = 
        pnVarCleanFromInput('project_name','project_description','project_leader');
    extract($args);

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $result = pnModAPIFunc('todolist','admin','createproject',
                        array('project_name' => $project_name,
                        'project_description' => $project_description,
                        'project_leader' => $project_leader));

    if ($result != false) {
        pnSessionSetVar('statusmsg', xarML('Project has been created'));
    }

    pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));

    return true;
}


function todolist_admin_modifyproject($args)
{
    list($project_id) = pnVarCleanFromInput('project_id');
    extract($args);

    $output = new pnHTML();

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getproject',array('project_id' => $project_id));

    if ($item == false) {
        $output->Text(xarML('No such project'));
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Edit Project'));

    $output->FormStart(pnModURL('todolist', 'admin', 'updateproject'));
    $output->FormHidden('authid', pnSecGenAuthKey());
    $output->FormHidden('new_project_id', pnVarPrepForDisplay($project_id));
    $output->TableStart();

    // Project Name
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project name')));
    $row[] = $output->FormText('new_project_name', pnVarPrepForDisplay($item['project_name']), 30, 30);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Project Description
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project description')));
    $row[] = $output->FormText('new_project_description', pnVarPrepForDisplay($item['project_description']), 30, 200);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Project Leader
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project leader')));
//    $row[] = $output->FormText('new_project_leader', pnVarPrepForDisplay($item['project_leader']), 30, 30);
    $row[] = $output->Text(makeUserDropdownList("new_project_leader",array(pnVarPrepForDisplay($item['project_leader'])),"all",false,false,''));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    $project_members = pnModAPIFunc('todolist','user','getprojectmembers',
                       array('project_id' => $project_id));

    // Project members
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Project members')));
    $row[] = $output->Text(makeUserDropdownList("new_project_members",$project_members,"all",false,true,''));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'LEFT');
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
function todolist_admin_updateproject($args)
{
    list($project_id,$project_name,$project_description,$project_leader,$project_members) = 
         pnVarCleanFromInput('new_project_id','new_project_name','new_project_description','new_project_leader','new_project_members');

    extract($args);
                            
    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', xarML('Load of module failed'));
        return $output->GetOutput();
    }

    if(pnModAPIFunc('todolist','admin','updateproject',
                    array('project_id' => $project_id,'project_name' => $project_name,
                    'project_description' => $project_description,'project_leader' => $project_leader,
                    'project_members' => $project_members))) {
        pnSessionSetVar('statusmsg', xarML('Updated'));
    }
    pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));

    return true;
}

function todolist_admin_deleteproject($args)
{
    list($project_id, $confirmation) = 
        pnVarCleanFromInput('project_id','confirmation');

    extract($args);

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getproject', array('project_id' => $project_id));

    if ($item == false) {
        $output->Text(xarML('No such project'));
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::Item', "$item[project_name]::$project_id", ACCESS_DELETE)) {
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
                               pnModURL('todolist','admin','deleteproject'),
                               xarML('Cancel deletion'),
                               pnModURL('todolist','admin','viewprojects'),
                               array('project_id' => $project_id));

        return $output->GetOutput();
    }

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    if (pnModAPIFunc('todolist','admin','deleteproject',
                     array('project_id' => $project_id))) {
        pnSessionSetVar('statusmsg', xarML('Project has been deleted'));
    }

    pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));
    
    return true;
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


function todolist_admin_modifygroup($args)
{
    list($group_id) = pnVarCleanFromInput('group_id');
    extract($args);

    $output = new pnHTML();

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getgroup',array('group_id' => $group_id));

    if ($item == false) {
        $output->Text(xarML('No such group'));
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Edit Group'));

    $output->FormStart(pnModURL('todolist', 'admin', 'updategroup'));
    $output->FormHidden('authid', pnSecGenAuthKey());
    $output->FormHidden('new_group_id', pnVarPrepForDisplay($group_id));
    $output->TableStart();

    // Group Name
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Group Name')));
    $row[] = $output->FormText('new_group_name', pnVarPrepForDisplay($item['group_name']), 30, 30);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Group Description
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Group description')));
    $row[] = $output->FormText('new_group_description', pnVarPrepForDisplay($item['group_description']), 30, 200);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Group Leader
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Group Leader')));
    $row[] = $output->Text(makeUserDropdownList("new_group_leader",array(pnVarPrepForDisplay($item['group_leader'])),"all",false,false,''));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    $group_members = pnModAPIFunc('todolist','user','getgroupmembers',
                       array('group_id' => $group_id));

    // Group members
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Group Members')));
    $row[] = $output->Text(makeUserDropdownList("new_group_members",$group_members,"all",false,true,''));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'LEFT');
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

function todolist_admin_viewgroups()
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
        $output->FormStart(pnModURL('todolist', 'admin', 'creategroup'));
        $output->FormHidden('authid', pnSecGenAuthKey());
        $output->TableStart(xarML('Add New Group'));

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Group Name')));
        $row[] = $output->FormText('group_name', '', 32, 32);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Group description')));
        $row[] = $output->FormText('group_description', '', 25, 25);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(xarML('Group Leader')));
        $row[] = $output->Text(makeUserDropdownList("group_leader",array(),"all",false,false,''));
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);
        $output->TableEnd();

        $output->Linebreak();
        $output->FormSubmit(xarML('Add'));
        $output->FormEnd();
    }

    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Groups'));

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(xarML('Load of module failed'));
        return $output->GetOutput();
    }

    $items = pnModAPIFunc('todolist','user','getallgroups',
                          array('startnum' => $startnum,
                          'numitems' => pnModGetVar('todolist','ITEMS_PER_PAGE')));

    $output->TableStart('',array(xarML('Id'), xarML('Group Name'), xarML('Action')), 3);

    foreach ($items as $item) {
        $row = array();
        if (pnSecAuthAction(0, 'todolist::', "$item[group_name]::$item[group_id]", ACCESS_READ)) {
            $row[] = $item['group_id'];
            $row[] = $item['group_name'];

            $options = array();
            $output->SetOutputMode(_PNH_RETURNOUTPUT);
            if (pnSecAuthAction(0, 'todolist::', "$item[group_name]::$item[group_id]", ACCESS_EDIT)) {
                $options[] = $output->URL(pnModURL('todolist','admin','modifygroup',
                                                   array('group_id' => $item['group_id'])), _EDIT);
                if (pnSecAuthAction(0, 'todolist::', "$item[group_name]::$item[group_id]", ACCESS_DELETE)) {
                    $options[] = $output->URL(pnModURL('todolist','admin','deletegroup',
                                                       array('group_id' => $item['group_id'])), xarML('Delete'));
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
                    pnModAPIFunc('todolist', 'user', 'countgroups'),
                    pnModURL('todolist','admin','viewgroups',array('startnum' => '%%')),
                    pnModGetVar('todolist', 'ITEMS_PER_PAGE'));

    return $output->GetOutput();
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

// This is a standard function to modify the configuration parameters of the module
function todolist_admin_modifyconfig()
{
    $output = new pnHTML();

    if (!pnSecAuthAction(0, 'todolist::', '::', ACCESS_ADMIN)) {
        $output->Text(xarML('Not authorised to access Todolist module'));
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(xarML('Modify Todolist module configuration'));
    $output->FormStart(pnModURL('todolist', 'admin', 'updateconfig'));
    $output->FormHidden('authid', pnSecGenAuthKey());

    $output->TableStart();

    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Access restricted')));
    $row[] = $output->FormText('ACCESS_RESTRICTED', pnModGetVar('todolist', 'ACCESS_RESTRICTED'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('BACKGROUND_COLOR (Default = #99CCFF)')));
    $row[] = $output->FormText('BACKGROUND_COLOR', pnModGetVar('todolist', 'BACKGROUND_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('DONE_COLOR (Default = #CCFFFF)')));
    $row[] = $output->FormText('DONE_COLOR', pnModGetVar('todolist', 'DONE_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('HIGH_COLOR (Default = #ffff00)')));
    $row[] = $output->FormText('HIGH_COLOR', pnModGetVar('todolist', 'HIGH_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('LOW_COLOR (Default = #66ccff)')));
    $row[] = $output->FormText('LOW_COLOR', pnModGetVar('todolist', 'LOW_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('MED_COLOR (Default = #FFcc66)')));
    $row[] = $output->FormText('MED_COLOR', pnModGetVar('todolist', 'MED_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('MOST_IMPORTANT_COLOR (Default = #FFFF99)')));
    $row[] = $output->FormText('MOST_IMPORTANT_COLOR', pnModGetVar('todolist', 'MOST_IMPORTANT_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('VERY_IMPORTANT_COLOR (Default = #FF3366)')));
    $row[] = $output->FormText('VERY_IMPORTANT_COLOR', pnModGetVar('todolist', 'VERY_IMPORTANT_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Dateformat: 1 = YYYY-MM-DD / 2 = DD.MM.JJJJJ / 3 = MM/DD/YYYY (Default - 2)')));
    $row[] = $output->FormText('DATEFORMAT', pnModGetVar('todolist', 'DATEFORMAT'), 1, 1);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Maximum number of done-entries shown on the main page.')));
    $row[] = $output->FormText('MAX_DONE', pnModGetVar('todolist', 'MAX_DONE'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Days in the past that should be higligted with VERY_IMPORTANT_COLOR and MOST_IMPORTANT_COLOR foreground-color (Disable = 0)')));
    $row[] = $output->FormText('MOST_IMPORTANT_DAYS', pnModGetVar('todolist', 'MOST_IMPORTANT_DAYS'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Refresh-time for the main page (Default = 600)')));
    $row[] = $output->FormText('REFRESH_MAIN', pnModGetVar('todolist', 'REFRESH_MAIN'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Should mails be send via local mailserver?')));
    $row[] = $output->FormText('SEND_MAILS', pnModGetVar('todolist', 'SEND_MAILS'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('If there is a note attached to the todo the number of notes attached is shown in the details column. To have another notification you can also show an asterisk in one of the left columns. Possible options are: 0 = disable extra asterisk, 1 = show it in #-column, 2 = show it in priority-column, 3 = show it in percentage completed-column, 4 = show it in text-column)')));
    $row[] = $output->FormText('SHOW_EXTRA_ASTERISK', pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK'), 1, 1);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show the line-Numbers? [true/false] (Default = true)')));
    $row[] = $output->FormText('SHOW_LINE_NUMBERS', pnModGetVar('todolist', 'SHOW_LINE_NUMBERS'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show percentage-completed in the tables? [true/false] (Default = true)')));
    $row[] = $output->FormText('SHOW_PERCENTAGE_IN_TABLE', pnModGetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Show priority as text in the tables ? [true/false] (Default = true)')));
    $row[] = $output->FormText('SHOW_PRIORITY_IN_TABLE', pnModGetVar('todolist', 'SHOW_PRIORITY_IN_TABLE'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML("Custom title. For example the Company's-Name")));
    $row[] = $output->FormText('TODO_HEADING', pnModGetVar('todolist', 'TODO_HEADING'), 30, 30);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Days in the future that should be higligted with VERY_IMPORTANT_COLOR (Disable = 0)')));
    $row[] = $output->FormText('VERY_IMPORTANT_DAYS', pnModGetVar('todolist', 'VERY_IMPORTANT_DAYS'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(xarML('Items per page')));
    $row[] = $output->FormText('ITEMS_PER_PAGE', pnModGetVar('todolist', 'ITEMS_PER_PAGE'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);


    $output->TableEnd();

    // End form
    $output->Linebreak(2);
    $output->FormSubmit(xarML('Update'));
    $output->FormEnd();
    
    return $output->GetOutput();
}

// This is a standard function to update the configuration parameters of the
// module given the information passed back by the modification form
function todolist_admin_updateconfig()
{
    $ACCESS_RESTRICTED = pnVarCleanFromInput('ACCESS_RESTRICTED');
    $BACKGROUND_COLOR = pnVarCleanFromInput('BACKGROUND_COLOR');
    $DATEFORMAT = pnVarCleanFromInput('DATEFORMAT');
    $DONE_COLOR = pnVarCleanFromInput('DONE_COLOR');
    $HIGH_COLOR = pnVarCleanFromInput('HIGH_COLOR');
    $LOW_COLOR = pnVarCleanFromInput('LOW_COLOR');
    $MAX_DONE = pnVarCleanFromInput('MAX_DONE');
    $MED_COLOR = pnVarCleanFromInput('MED_COLOR');
    $MOST_IMPORTANT_COLOR = pnVarCleanFromInput('MOST_IMPORTANT_COLOR');
    $MOST_IMPORTANT_DAYS = pnVarCleanFromInput('MOST_IMPORTANT_DAYS');
    $REFRESH_MAIN = pnVarCleanFromInput('REFRESH_MAIN');
    $SEND_MAILS = pnVarCleanFromInput('SEND_MAILS');
    $SHOW_EXTRA_ASTERISK = pnVarCleanFromInput('SHOW_EXTRA_ASTERISK');
    $SHOW_LINE_NUMBERS = pnVarCleanFromInput('SHOW_LINE_NUMBERS');
    $SHOW_PERCENTAGE_IN_TABLE = pnVarCleanFromInput('SHOW_PERCENTAGE_IN_TABLE');
    $SHOW_PRIORITY_IN_TABLE = pnVarCleanFromInput('SHOW_PRIORITY_IN_TABLE');
    $TODO_HEADING = pnVarCleanFromInput('TODO_HEADING');
    $VERY_IMPORTANT_COLOR = pnVarCleanFromInput('VERY_IMPORTANT_COLOR');
    $VERY_IMPORTANT_DAYS = pnVarCleanFromInput('VERY_IMPORTANT_DAYS');
    $ITEMS_PER_PAGE = pnVarCleanFromInput('ITEMS_PER_PAGE');

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', xarML('Bad Auth Key'));
        pnRedirect(pnModURL('todolist', 'admin', 'view'));
        return true;
    }

    if (!isset($ACCESS_RESTRICTED)) $ACCESS_RESTRICTED = "";
    if (!isset($BACKGROUND_COLOR)) $BACKGROUND_COLOR ="#99ccff";
    if (!isset($DATEFORMAT)) $DATEFORMAT = "1";
    if (!isset($DONE_COLOR)) $DONE_COLOR = "#ccffff";
    if (!isset($DONE_COLOR)) $DONE_COLOR = "#ffff00";
    if (!isset($LOW_COLOR)) $LOW_COLOR = "#66ccff";
    if (!isset($MAX_DONE)) $MAX_DONE = 10;
    if (!isset($MED_COLOR)) $MED_COLOR = "#ffcc66";
    if (!isset($MOST_IMPORTANT_COLOR)) $MOST_IMPORTANT_COLOR = "#ffff99";
    if (!isset($MOST_IMPORTANT_DAYS)) $MOST_IMPORTANT_DAYS = 3;
    if (!isset($REFRESH_MAIN)) $REFRESH_MAIN = 600;
    if (!isset($SEND_MAILS)) $SEND_MAILS = true;
    if (!isset($SHOW_EXTRA_ASTERISK)) $SHOW_EXTRA_ASTERISK = 1;
    if (!isset($SHOW_LINE_NUMBERS)) $SHOW_LINE_NUMBERS = true;
    if (!isset($SHOW_PERCENTAGE_IN_TABLE)) $SHOW_PERCENTAGE_IN_TABLE = true;
    if (!isset($SHOW_PRIORITY_IN_TABLE)) $SHOW_PRIORITY_IN_TABLE = true;
    if (!isset($TODO_HEADING)) $TODO_HEADING = "Todolist";
    if (!isset($VERY_IMPORTANT_COLOR)) $VERY_IMPORTANT_COLOR = "#ff3366";
    if (!isset($VERY_IMPORTANT_DAYS)) $VERY_IMPORTANT_DAYS = 3;
    if (!isset($ITEMS_PER_PAGE)) $ITEMS_PER_PAGE = 20;

    pnModSetVar('todolist', 'ACCESS_RESTRICTED', $ACCESS_RESTRICTED);
    pnModSetVar('todolist', 'BACKGROUND_COLOR', $BACKGROUND_COLOR);
    pnModSetVar('todolist', 'DATEFORMAT', $DATEFORMAT);
    pnModSetVar('todolist', 'DONE_COLOR', $DONE_COLOR);
    pnModSetVar('todolist', 'HIGH_COLOR', $HIGH_COLOR);
    pnModSetVar('todolist', 'LOW_COLOR', $LOW_COLOR);
    pnModSetVar('todolist', 'MAX_DONE', $MAX_DONE);
    pnModSetVar('todolist', 'MED_COLOR', $MED_COLOR);
    pnModSetVar('todolist', 'MOST_IMPORTANT_COLOR', $MOST_IMPORTANT_COLOR);
    pnModSetVar('todolist', 'MOST_IMPORTANT_DAYS', $MOST_IMPORTANT_DAYS);
    pnModSetVar('todolist', 'REFRESH_MAIN', $REFRESH_MAIN);
    pnModSetVar('todolist', 'SEND_MAILS', $SEND_MAILS);
    pnModSetVar('todolist', 'SHOW_EXTRA_ASTERISK', $SHOW_EXTRA_ASTERISK);
    pnModSetVar('todolist', 'SHOW_LINE_NUMBERS', $SHOW_LINE_NUMBERS);
    pnModSetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE', $SHOW_PERCENTAGE_IN_TABLE);
    pnModSetVar('todolist', 'SHOW_PRIORITY_IN_TABLE', $SHOW_PRIORITY_IN_TABLE);
    pnModSetVar('todolist', 'TODO_HEADING', $TODO_HEADING);
    pnModSetVar('todolist', 'VERY_IMPORTANT_COLOR', $VERY_IMPORTANT_COLOR);
    pnModSetVar('todolist', 'VERY_IMPORTANT_DAYS', $VERY_IMPORTANT_DAYS);
    pnModSetVar('todolist', 'ITEMS_PER_PAGE', $ITEMS_PER_PAGE);

    pnRedirect(pnModURL('todolist', 'admin', 'main'));

    return true;
}
?>