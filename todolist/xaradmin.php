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
include_once('modules/'.pnVarPrepForOS($modinfo['directory']).'/pnadmin-functions.php'); 

function todolist_admin_main()
{
    $output = new pnHTML();

    if (!pnSecAuthAction(0, 'todolist::Item', '::', ACCESS_EDIT)) {
        $output->Text(_TODOLIST_NOAUTH);
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
	$output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(pnGetStatusMsg());
    $output->Linebreak();

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $output->TableStart(_TODOLIST_ADMIN, '', 1);
    $output->TableRowStart();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewusers'),_TODOLIST_VIEWUSERS); 
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewgroups'),_TODOLIST_VIEWGROUPS); 
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','viewprojects'),_TODOLIST_VIEWPROJECTS); 
    $output->TableColEnd();
    $output->TableColStart();
    $output->URL(pnModURL('todolist','admin','modifyconfig'),_TODOLIST_EDITCONFIG); 
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
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', _TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $result = pnModAPIFunc('todolist','admin','createproject',
                        array('project_name' => $project_name,
                        'project_description' => $project_description,
                        'project_leader' => $project_leader));

    if ($result != false) {
        pnSessionSetVar('statusmsg', _TODOLIST_PROJECT_CREATED);
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
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getproject',array('project_id' => $project_id));

    if ($item == false) {
        $output->Text(_TODOLIST_NOSUCHPROJECT);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(_TODOLIST_EDITPROJECT);

    $output->FormStart(pnModURL('todolist', 'admin', 'updateproject'));
    $output->FormHidden('authid', pnSecGenAuthKey());
    $output->FormHidden('new_project_id', pnVarPrepForDisplay($project_id));
    $output->TableStart();

    // Project Name
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_PROJECT_NAME));
    $row[] = $output->FormText('new_project_name', pnVarPrepForDisplay($item['project_name']), 30, 30);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Project Description
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_PROJECT_DESCRIPTION));
    $row[] = $output->FormText('new_project_description', pnVarPrepForDisplay($item['project_description']), 30, 200);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Project Leader
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_PROJECT_LEADER));
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
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_PROJECT_MEMBERS));
    $row[] = $output->Text(makeUserDropdownList("new_project_members",$project_members,"all",false,true,''));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'LEFT');
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->TableEnd();
    $output->Linebreak(2);
    $output->FormSubmit(_TODOLIST_UPDATE);
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
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', _TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    if(pnModAPIFunc('todolist','admin','updateproject',
                    array('project_id' => $project_id,'project_name' => $project_name,
                    'project_description' => $project_description,'project_leader' => $project_leader,
                    'project_members' => $project_members))) {
        pnSessionSetVar('statusmsg', _TODOLIST_UPDATED);
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
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getproject', array('project_id' => $project_id));

    if ($item == false) {
        $output->Text(_TODOLIST_NOSUCHPROJECT);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::Item', "$item[project_name]::$project_id", ACCESS_DELETE)) {
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    if (empty($confirmation)) {
        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(todolist_adminmenu());
        $output->SetInputMode(_PNH_PARSEINPUT);

        $output->Title(_TODOLIST_DELETE);

        $output->ConfirmAction(_TODOLIST_CONFIRM_DELETE,
                               pnModURL('todolist','admin','deleteproject'),
                               _TODOLIST_CANCEL_DELETE,
                               pnModURL('todolist','admin','viewprojects'),
                               array('project_id' => $project_id));

        return $output->GetOutput();
    }

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewprojects'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    if (pnModAPIFunc('todolist','admin','deleteproject',
                     array('project_id' => $project_id))) {
        pnSessionSetVar('statusmsg', _TODOLIST_PROJECT_DELETED);
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
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());

    if (pnSecAuthAction(0, 'todolist::', '::', ACCESS_ADD)) {
        $output->FormStart(pnModURL('todolist', 'admin', 'createproject'));
        $output->FormHidden('authid', pnSecGenAuthKey());
        $output->TableStart(_TODOLIST_ADDPROJECT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_PROJECT_NAME));
        $row[] = $output->FormText('project_name', '', 32, 32);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_PROJECT_DESCRIPTION));
        $row[] = $output->FormText('project_description', '', 25, 25);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_PROJECT_LEADER));
        $row[] = $output->Text(makeUserDropdownList("project_leader",array(),"all",false,false,''));
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);
        $output->TableEnd();

        $output->Linebreak();
        $output->FormSubmit(_TODOLIST_ADD);
        $output->FormEnd();
    }

    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(_TODOLIST_VIEWPROJECTS);

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $items = pnModAPIFunc('todolist','user','getallprojects',
                          array('startnum' => $startnum,
                          'numitems' => pnModGetVar('todolist','ITEMS_PER_PAGE')));

    $output->TableStart('',array(_TODOLIST_PROJECT_ID, _TODOLIST_PROJECT_NAME, _TODOLIST_ACTION), 3);

    foreach ($items as $item) {
        $row = array();
        if (pnSecAuthAction(0, 'todolist::', "$item[project_name]::$item[project_id]", ACCESS_READ)) {
            $row[] = $item['project_id'];
            $row[] = $item['project_name'];

            $options = array();
            $output->SetOutputMode(_PNH_RETURNOUTPUT);
            if (pnSecAuthAction(0, 'todolist::', "$item[project_name]::$item[project_id]", ACCESS_EDIT)) {
                $options[] = $output->URL(pnModURL('todolist','admin','modifyproject',
                             array('project_id' => $item['project_id'])), _TODOLIST_EDIT);
                if (pnSecAuthAction(0, 'todolist::', "$item[project_name]::$item[project_id]", ACCESS_DELETE)) {
                    $options[] = $output->URL(pnModURL('todolist','admin','deleteproject',
                             array('project_id' => $item['project_id'])), _TODOLIST_DELETE);
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
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', _TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $result = pnModAPIFunc('todolist','admin','creategroup',
                        array('group_name' => $group_name,
                        'group_description' => $group_description,
                        'group_leader' => $group_leader));

    if ($result != false) {
        pnSessionSetVar('statusmsg', _TODOLIST_GROUP_CREATED);
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
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getgroup',array('group_id' => $group_id));

    if ($item == false) {
        $output->Text(_TODOLIST_NOSUCHGROUP);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(_TODOLIST_EDITGROUP);

    $output->FormStart(pnModURL('todolist', 'admin', 'updategroup'));
    $output->FormHidden('authid', pnSecGenAuthKey());
    $output->FormHidden('new_group_id', pnVarPrepForDisplay($group_id));
    $output->TableStart();

    // Group Name
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_GROUP_NAME));
    $row[] = $output->FormText('new_group_name', pnVarPrepForDisplay($item['group_name']), 30, 30);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Group Description
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_GROUP_DESCRIPTION));
    $row[] = $output->FormText('new_group_description', pnVarPrepForDisplay($item['group_description']), 30, 200);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Group Leader
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_GROUP_LEADER));
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
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_GROUP_MEMBERS));
    $row[] = $output->Text(makeUserDropdownList("new_group_members",$group_members,"all",false,true,''));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->TableAddrow($row, 'LEFT');
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->TableEnd();
    $output->Linebreak(2);
    $output->FormSubmit(_TODOLIST_UPDATE);
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
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', _TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    if(pnModAPIFunc('todolist','admin','updategroup',
                    array('group_id' => $group_id,'group_name' => $group_name,
                    'group_description' => $group_description,'group_leader' => $group_leader,
                    'group_members' => $group_members))) {
        pnSessionSetVar('statusmsg', _TODOLIST_UPDATED);
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
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getgroup', array('group_id' => $group_id));

    if ($item == false) {
        $output->Text(_TODOLIST_NOSUCHGROUP);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_DELETE)) {
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    if (empty($confirmation)) {
        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(todolist_adminmenu());
        $output->SetInputMode(_PNH_PARSEINPUT);

        $output->Title(_TODOLIST_DELETE);

        $output->ConfirmAction(_TODOLIST_CONFIRM_DELETE,
                               pnModURL('todolist','admin','deletegroup'),
                               _TODOLIST_CANCEL_DELETE,
                               pnModURL('todolist','admin','viewgroups'),
                               array('group_id' => $group_id));

        return $output->GetOutput();
    }

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewgroups'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    if (pnModAPIFunc('todolist','admin','deletegroup',
                     array('group_id' => $group_id))) {
        pnSessionSetVar('statusmsg', _TODOLIST_GROUP_DELETED);
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
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());

    if (pnSecAuthAction(0, 'todolist::', '::', ACCESS_ADD)) {
        $output->FormStart(pnModURL('todolist', 'admin', 'creategroup'));
        $output->FormHidden('authid', pnSecGenAuthKey());
        $output->TableStart(_TODOLIST_ADDGROUP);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_GROUP_NAME));
        $row[] = $output->FormText('group_name', '', 32, 32);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_GROUP_DESCRIPTION));
        $row[] = $output->FormText('group_description', '', 25, 25);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_GROUP_LEADER));
        $row[] = $output->Text(makeUserDropdownList("group_leader",array(),"all",false,false,''));
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);
        $output->TableEnd();

        $output->Linebreak();
        $output->FormSubmit(_TODOLIST_ADD);
        $output->FormEnd();
    }

    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(_TODOLIST_VIEWGROUPS);

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $items = pnModAPIFunc('todolist','user','getallgroups',
                          array('startnum' => $startnum,
                          'numitems' => pnModGetVar('todolist','ITEMS_PER_PAGE')));

    $output->TableStart('',array(_TODOLIST_GROUP_ID, _TODOLIST_GROUP_NAME, _TODOLIST_ACTION), 3);

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
                                                       array('group_id' => $item['group_id'])), _TODOLIST_DELETE);
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
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', _TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $result = pnModAPIFunc('todolist','admin','createuser',
                        array('user_id' => $user_id,
                        'user_email_notify' => $user_email_notify,
                        'user_primary_project' => $user_primary_project,
                        'user_my_tasks' => $user_my_tasks,
                        'user_show_icons' => $user_show_icons));

    if ($result != false) {
        pnSessionSetVar('statusmsg', _TODOLIST_USER_CREATED);
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
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getuser',array('user_id' => $user_id));

    if ($item == false) {
        $output->Text(_TODOLIST_NOSUCHUSER);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_EDIT)) {
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(_TODOLIST_EDITUSER);

    $output->FormStart(pnModURL('todolist', 'admin', 'updateuser'));
    $output->FormHidden('authid', pnSecGenAuthKey());
    $output->FormHidden('new_user_id', pnVarPrepForDisplay($user_id));
    $output->TableStart();

    // Email notify
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_NAME));
    $row[] = $output->Text(pnVarPrepForDisplay(pnUserGetVar('uname',$item['user_id'])));
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Email notify
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_EMAIL_NOTIFY));
    $row[] = $output->FormText('new_user_email_notify', pnVarPrepForDisplay($item['user_email_notify']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // Primary Project
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_PRIMARY_PROJECT));
    $row[] = $output->FormText('new_user_primary_project', pnVarPrepForDisplay($item['user_primary_project']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    // My Tasks
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_MY_TASKS));
    $row[] = $output->FormText('new_user_my_tasks', pnVarPrepForDisplay($item['user_my_tasks']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);


    // Show Icons
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_SHOW_ICONS));
    $row[] = $output->FormText('new_user_show_icons', pnVarPrepForDisplay($item['user_show_icons']), 20, 20);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->TableEnd();
    $output->Linebreak(2);
    $output->FormSubmit(_TODOLIST_UPDATE);
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
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        pnSessionSetVar('errormsg', _TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    if(pnModAPIFunc('todolist','admin','updateuser',
                        array('user_email_notify' => $user_email_notify,
                        'user_primary_project' => $user_primary_project,
                        'user_my_tasks' => $user_my_tasks,
                        'user_show_icons' => $user_show_icons))) {
        pnSessionSetVar('statusmsg', _TODOLIST_UPDATED);
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
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $item = pnModAPIFunc('todolist','user','getuser', array('user_id' => $user_id));

    if ($item == false) {
        $output->Text(_TODOLIST_NOSUCHUSER);
        return $output->GetOutput();
    }

    if (!pnSecAuthAction(0, 'todolist::', "::", ACCESS_DELETE)) {
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    if (empty($confirmation)) {
        $output = new pnHTML();

        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->Text(todolist_adminmenu());
        $output->SetInputMode(_PNH_PARSEINPUT);

        $output->Title(_TODOLIST_DELETE);

        $output->ConfirmAction(_TODOLIST_CONFIRM_DELETE,
                               pnModURL('todolist','admin','deleteuser'),
                               _TODOLIST_CANCEL_DELETE,
                               pnModURL('todolist','admin','viewusers'),
                               array('user_id' => $user_id));

        return $output->GetOutput();
    }

    if (!pnSecConfirmAuthKey()) {
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
        pnRedirect(pnModURL('todolist', 'admin', 'viewusers'));
        return true;
    }

    if (!pnModAPILoad('todolist', 'admin')) {
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    if (pnModAPIFunc('todolist','admin','deleteuser',
                     array('user_id' => $user_id))) {
        pnSessionSetVar('statusmsg', _TODOLIST_USER_DELETED);
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
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());

    if (pnSecAuthAction(0, 'todolist::', '::', ACCESS_ADD)) {
        $output->FormStart(pnModURL('todolist', 'admin', 'createuser'));
        $output->FormHidden('authid', pnSecGenAuthKey());
        $output->TableStart(_TODOLIST_ADDUSER);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER));
        $row[] = $output->Text(makeUserDropdownList("user_id",array(),"all",false,false,'all'));
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_EMAIL_NOTIFY));
        $row[] = $output->FormText('user_email_notify', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_PRIMARY_PROJECT));
        $row[] = $output->FormText('user_primary_project', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_MY_TASKS));
        $row[] = $output->FormText('user_my_tasks', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $row = array();
        $output->SetOutputMode(_PNH_RETURNOUTPUT);
        $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_USER_SHOW_ICONS));
        $row[] = $output->FormText('user_show_icons', '', 20, 20);
        $output->SetOutputMode(_PNH_KEEPOUTPUT);
        $output->SetInputMode(_PNH_VERBATIMINPUT);
        $output->TableAddrow($row, 'LEFT');
        $output->SetInputMode(_PNH_PARSEINPUT);

        $output->TableEnd();

        $output->Linebreak();
        $output->FormSubmit(_TODOLIST_ADD);
        $output->FormEnd();
    }

    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(_TODOLIST_VIEWUSERS);

    if (!pnModAPILoad('todolist', 'user')) {
        $output->Text(_TODOLIST_LOADFAILED);
        return $output->GetOutput();
    }

    $items = pnModAPIFunc('todolist','user','getallusers',
                          array('startnum' => $startnum,
                          'numitems' => pnModGetVar('todolist','ITEMS_PER_PAGE')));

    $output->TableStart('',array(_TODOLIST_USER_NAME, _TODOLIST_USER_EMAIL_NOTIFY, _TODOLIST_USER_PRIMARY_PROJECT,
    _TODOLIST_USER_MY_TASKS, _TODOLIST_USER_SHOW_ICONS, _TODOLIST_ACTION), 6);

    foreach ($items as $item) {
        $row = array();
        if (pnSecAuthAction(0, 'todolist::', "$item[user_name]::$item[user_id]", ACCESS_READ)) {
            $row[] = pnUserGetVar('uname',$item['user_id']);
            $row[] = $item['user_email_notify'];
            $row[] = $item['user_primary_project'];
            $row[] = $item['user_my_tasks'];
            $row[] = $item['user_show_icons'];

            $options = array();
            $output->SetOutputMode(_PNH_RETURNOUTPUT);
            if (pnSecAuthAction(0, 'todolist::', "$item[user_name]::$item[user_id]", ACCESS_EDIT)) {
                $options[] = $output->URL(pnModURL('todolist','admin','modifyuser',
                                                   array('user_id' => $item['user_id'])), _TODOLIST_EDIT);
                if (pnSecAuthAction(0, 'todolist::', "$item[user_name]::$item[user_id]", ACCESS_DELETE)) {
                    $options[] = $output->URL(pnModURL('todolist','admin','deleteuser',
                                                       array('user_id' => $item['user_id'])), _TODOLIST_DELETE);
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
        $output->Text(_TODOLIST_NOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->Text(todolist_adminmenu());
    $output->SetInputMode(_PNH_PARSEINPUT);

    $output->Title(_TODOLIST_MODIFYCONFIG);
    $output->FormStart(pnModURL('todolist', 'admin', 'updateconfig'));
    $output->FormHidden('authid', pnSecGenAuthKey());

    $output->TableStart();

    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_ACCESS_RESTRICTED));
    $row[] = $output->FormText('ACCESS_RESTRICTED', pnModGetVar('todolist', 'ACCESS_RESTRICTED'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_BACKGROUND_COLOR));
    $row[] = $output->FormText('BACKGROUND_COLOR', pnModGetVar('todolist', 'BACKGROUND_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_DONE_COLOR));
    $row[] = $output->FormText('DONE_COLOR', pnModGetVar('todolist', 'DONE_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_HIGH_COLOR));
    $row[] = $output->FormText('HIGH_COLOR', pnModGetVar('todolist', 'HIGH_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_LOW_COLOR));
    $row[] = $output->FormText('LOW_COLOR', pnModGetVar('todolist', 'LOW_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_MED_COLOR));
    $row[] = $output->FormText('MED_COLOR', pnModGetVar('todolist', 'MED_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_MOST_IMPORTANT_COLOR));
    $row[] = $output->FormText('MOST_IMPORTANT_COLOR', pnModGetVar('todolist', 'MOST_IMPORTANT_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_VERY_IMPORTANT_COLOR));
    $row[] = $output->FormText('VERY_IMPORTANT_COLOR', pnModGetVar('todolist', 'VERY_IMPORTANT_COLOR'), 7, 7);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_DATEFORMAT_NUMBER));
    $row[] = $output->FormText('DATEFORMAT', pnModGetVar('todolist', 'DATEFORMAT'), 1, 1);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_MAX_DONE));
    $row[] = $output->FormText('MAX_DONE', pnModGetVar('todolist', 'MAX_DONE'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_MOST_IMPORTANT_DAYS));
    $row[] = $output->FormText('MOST_IMPORTANT_DAYS', pnModGetVar('todolist', 'MOST_IMPORTANT_DAYS'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_REFRESH_MAIN));
    $row[] = $output->FormText('REFRESH_MAIN', pnModGetVar('todolist', 'REFRESH_MAIN'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_SEND_MAILS));
    $row[] = $output->FormText('SEND_MAILS', pnModGetVar('todolist', 'SEND_MAILS'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_SHOW_EXTRA_ASTERISK));
    $row[] = $output->FormText('SHOW_EXTRA_ASTERISK', pnModGetVar('todolist', 'SHOW_EXTRA_ASTERISK'), 1, 1);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_SHOW_LINE_NUMBERS));
    $row[] = $output->FormText('SHOW_LINE_NUMBERS', pnModGetVar('todolist', 'SHOW_LINE_NUMBERS'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_SHOW_PERCENTAGE_IN_TABLE));
    $row[] = $output->FormText('SHOW_PERCENTAGE_IN_TABLE', pnModGetVar('todolist', 'SHOW_PERCENTAGE_IN_TABLE'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_SHOW_PRIORITY_IN_TABLE));
    $row[] = $output->FormText('SHOW_PRIORITY_IN_TABLE', pnModGetVar('todolist', 'SHOW_PRIORITY_IN_TABLE'), 5, 5);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_TODO_HEADING));
    $row[] = $output->FormText('TODO_HEADING', pnModGetVar('todolist', 'TODO_HEADING'), 30, 30);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_VERY_IMPORTANT_DAYS));
    $row[] = $output->FormText('VERY_IMPORTANT_DAYS', pnModGetVar('todolist', 'VERY_IMPORTANT_DAYS'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);
    $row = array();
    $output->SetOutputMode(_PNH_RETURNOUTPUT);
    $row[] = $output->Text(pnVarPrepForDisplay(_TODOLIST_ITEMSPERPAGE));
    $row[] = $output->FormText('ITEMS_PER_PAGE', pnModGetVar('todolist', 'ITEMS_PER_PAGE'), 3, 3);
    $output->SetOutputMode(_PNH_KEEPOUTPUT);
    $output->SetInputMode(_PNH_VERBATIMINPUT);
    $output->TableAddrow($row, 'left');
    $output->SetInputMode(_PNH_PARSEINPUT);


    $output->TableEnd();

    // End form
    $output->Linebreak(2);
    $output->FormSubmit(_TODOLIST_UPDATE);
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
        pnSessionSetVar('errormsg', _TODOLIST_BADAUTHKEY);
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