<?php
// 
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (C) 2001 by the PostNuke Development Team.
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
// Purpose of file: Group administration
// ----------------------------------------------------------------------

/**
 * the main administration function - pass-thru
 */
function xproject_groups_main()
{
    $output = new xarHTML();

    // auth check
    if (!xarSecAuthAction(0, 'Groups::', '::', ACCESS_DELETE)) {
        $output->Text(_GROUPSNOAUTH);
        return $output->GetOutput();
    }

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if(($func == "main" || empty($func)) && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());

    return $output->GetOutput();
}

/**
 * view groups
 */
function xproject_groups_viewallgroups()
{
    $output = new xarHTML();

	xarSessionSetVar('groupid',0);
	xarSessionDelVar('grouxarame');
	
    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "viewallgroups" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());


    if (!xarModAPILoad('xproject', 'groups')) {
		xarSessionSetVar('errormsg', _APILOADFAILED);
		return $output->GetOutput();
    }
    $groups = xarModAPIFunc('xproject',
			   'groups',
			   'getall');

    $tableHead = array(_GROUP, _OPTION);

    $output->TableStart('', $tableHead, 1);

    foreach($groups as $group) {

	$actions = array();
	$output->SetOutputMode(_XH_RETURNOUTPUT);

	if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_EDIT)) {
	    $grouxaramedisplay = $output->URL(xarModURL('xproject',
											   'groups',
											   'viewgroup', array('gid'   => $group['gid'],
													  'gname' => $group['name'])), xarVarPrepForDisplay($group['name']));
	} else {
		$grouxaramedisplay = $output->Text(xarVarPrepForDisplay($group['name']));
	}
	
	if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_EDIT)) {
	    $actions[] = $output->URL(xarModURL('xproject',
					       'groups',
					       'modifygroup', array('gid'   => $group['gid'],
								    'gname' => $group['name'])), _RENAMEGROUP);

	}
	if (xarSecAuthAction(0, 'Groups::', "$group[name]::$group[gid]", ACCESS_DELETE)) {
	    $actions[] = $output->URL(xarModURL('xproject',
					       'groups',
					       'deletegroup', array('gid'    => $group['gid'],
								    'gname'  => $group['name'],
								    'authid' => xarSecGenAuthKey())), _DELETE);
	}
	$output->SetOutputMode(_XH_KEEPOUTPUT);

	$actions = join(' | ', $actions);

	$row = array($grouxaramedisplay,
		     $actions);

	$output->SetInputMode(_XH_VERBATIMINPUT);
	$output->TableAddRow($row);
	$output->SetInputMode(_XH_PARSEINPUT);
    }
    $output->TableEnd();

    return $output->GetOutput();
}

/*
 * viewgroup - view a group
 */
function xproject_groups_viewgroup($args)
{
	extract($args);
	
	if(!xarModAPILoad('xproject','groups')) {
        xarSessionSetVar('errormsg', _PMLOGLOADFAILED);
        return false;
	}
	
	if (!isset($gid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }
	
    $output = new xarHTML();

	if(empty($groupid)) $groupid = xarVarCleanFromInput('gid');
	xarSessionSetVar('groupid',$groupid);
	

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "viewgroup" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());
    $output->LineBreak();

	$group = xarModAPIFunc('xproject','groups','get',array('gid' => $gid));
	
    $output->Title(_USERSINGROUP .': '.  xarVarPrepForDisplay($group['gname']));
    $output->URL(xarModURL('xproject',
						  'groups',
						  'adduser', array('gid' => $group['gid'])),
				_ADDUSERTOGROUP);
    $output->LineBreak();
    $output->SetInputMode(_XH_PARSEINPUT);

    $tableHead = array(_USERNAME, _OPTION);

    $output->TableStart('', $tableHead, 1);

    if (!xarModAPILoad('xproject', 'groups')) {
		xarSessionSetVar('errormsg', _APILOADFAILED);
		return $output->GetOutput();
    }
	
    $users = xarModAPIFunc('xproject',
			  'groups',
			  'getmembers', array('gid' => $group['gid']));
			  
    if ($users == false) {
		$output->Text('No users in this group');
		$output->LineBreak();
		$output->TableEnd();
		return $output->GetOutput();
    }
	
    foreach($users as $user) {
	
		$output->SetOutputMode(_XH_RETURNOUTPUT);
		if (xarSecAuthAction(0, 'Groups::', "::", ACCESS_DELETE)) {
			$action = $output->URL(xarModURL('xproject',
											   'groups',
											   'deleteuser', array('gid'    => $group['gid'],
																   'uid'    => $user['uid'],
																   'authid' => xarSecGenAuthKey())), _DELETE);
		}
		$output->SetOutputMode(_XH_KEEPOUTPUT);
	
		$row = array(xarVarPrepForDisplay($user['uname']), $action);
	
		$output->SetInputMode(_XH_VERBATIMINPUT);
		$output->TableAddRow($row);
		$output->SetInputMode(_XH_PARSEINPUT);
    }
    $output->TableEnd();

    return $output->GetOutput();
}

/*
 * newGroup - create a new group
 * Takes no parameters
 */
function xproject_groups_newgroup()
{
    $output = new xarHTML();

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "newgroup" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());

    if (!xarSecAuthAction(0, 'Groups::', '::', ACCESS_ADD)) {
        $output->Text(_GROUPSADDNOAUTH);
        return $output->GetOutput();
    }
    $output->FormStart(xarModURL('xproject', 'groups', 'addgroup'));
    $output->LineBreak();
    $output->Text(_GROUXARAME);
    $output->FormText('gname', '', 20, 20);
    $output->FormHidden('authid', xarSecGenAuthKey());
    $output->LineBreak(2);
    $output->FormSubmit(_NEWGROUP);
    $output->FormEnd();

    return $output->GetOutput();
}

/*
 * addGroup - add a group
 */
function xproject_groups_addgroup()
{
    $output = new xarHTML();

    $gname = xarVarCleanFromInput('gname');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
	
    if (!xarModAPILoad('groups', 'admin')) {
		xarSessionSetVar('errormsg', _APILOADFAILED);
		return $output-GetOutput();
    }
	
    $gname = xarModAPIFunc('xproject',
			  'groups',
			  'addgroup', array('gname' => $gname));

    if ($gname == false) {
		xarSessionSetVar('errormsg', _GROUPALREADYEXISTS);
		return $output->GetOutput();
    }
	
    xarSessionSetVar('statusmsg', _GROUPADDED);

    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
}

/*
 * deletegroup - delete a group
 * prompts for confirmation
 */
function xproject_groups_deletegroup()
{
    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
    list($gid,
		 $gname,
		 $confirmation) = xarVarCleanFromInput('gid',
											  'gname',
											  'confirmation');

    if (!xarSecAuthAction(0, 'Groups::', "$gname::$gid", ACCESS_DELETE)) {
		xarSessionSetVar('errormsg', _GROUPSDELNOAUTH);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
	
    if (!xarModAPILoad('xproject', 'groups')) {
		xarSessionSetVar('errormsg', _APILOADFAILED);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
	
    if (empty($confirmation)) {

		$output = new xarHTML();
		
		$func = xarVarCleanFromInput('func');
		if($func == "new" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());
        $output->ConfirmAction(_DELETEGROUPSURE,
                               xarModURL('xproject',
                                        'groups',
                                        'deletegroup'),
                               _CANCEL,
                               xarModURL('xproject',
                                        'groups',
                                        'main'),
                               array('gid' => $gid));
	
		return $output->GetOutput();
    }
    if (xarModAPIFunc('xproject',
		     'groups',
		     'deletegroup', array('gid' => $gid))) {

		xarSessionSetVar('statusmsg', _GROUPDELETED);
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}

/*
 * adduser - user selection for a group
 */
function xproject_groups_adduser()
{
    $output = new xarHTML();

    $gid = xarVarCleanFromInput('gid');

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "adduser" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());
    $output->LineBreak();
	
    if (!xarModAPILoad('xproject', 'groups')) {
		xarSessionSetVar('errormsg', _APILOADFAILED);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
	
	$group = xarModAPIFunc('xproject','groups','get',array('gid' => $gid));

    $output->SetInputMode(_XH_VERBATIMINPUT);
	
    $output->Title(_USERTOADD .' :: '. xarVarPrepForDisplay($group['gname']));
    $output->SetInputMode(_XH_PARSEINPUT);

    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_ADD)) {
		xarSessionSetVar('errormsg', _GROUPSADDNOAUTH);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
	
    $users = (xarModAPIFunc('xproject', 'groups', 'getmembers', array('eid' => $gid)));

    if($users == false) {
		$output->Text(_PMLOGMEMBERSFAILED);
		return $output->GetOutput();
    }
	
    $output->TableStart(xarSessionGetVar('tempvar'));
	xarSessionDelVar('tempvar');
    $output->FormStart(xarModURL('xproject', 'groups', 'insertuser'));
    $output->FormHidden('gid', $group['gid']);
    $output->FormHidden('authid', xarSecGenAuthKey());
    $userlist = array();

    foreach($users as $user) {
	$userlist[] = array('id' => $user['uid'],
			    'name' => $user['uname']);
    }
    $row = array();
    $output->SetOutputMode(_XH_RETURNOUTPUT);
    $row[] = $output->FormSelectMultiple('uid', $userlist);
    $row[] = $output->FormSubmit(_ADDUSER);
    $output->SetOutputMode(_XH_KEEPOUTPUT);

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $output->TableAddRow($row);
    $output->SetInputMode(_XH_PARSEINPUT);

    $output->FormEnd();
    $output->TableEnd();
    return $output->GetOutput();
}
/*
 * insertuser - insert a user into a group
 */
function xproject_groups_insertuser()
{
    list($gid,
	 $uid) = xarVarCleanFromInput('gid',
				    'uid');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
	
    if (!xarModAPILoad('xproject', 'groups')) {
		xarSessionSetVar('errormsg', _APILOADFAILED);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
	
    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_ADD)) {
		xarSessionSetVar('errormsg', _GROUPSDELNOAUTH);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
	
    if (xarModAPIFunc('xproject', 
					'groups',
					'insertuser', array('gid' => $gid,
										'uid' => $uid))) {

		xarSessionSetVar('statusmsg', _USERADDED);
    }
	
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
}

/*
 * deleteuser - delete a user from a group
 */
function xproject_groups_deleteuser()
{
	list($gid,
		 $uid,
		 $confirmation) = xarVarCleanFromInput('gid',
											  'uid',
											  'confirmation');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
    if (!xarModAPILoad('xproject', 'groups')) {
		xarSessionSetVar('errormsg', _APILOADFAILED);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_DELETE)) {
		xarSessionSetVar('errormsg', _GROUPSDELNOAUTH);
		xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
    if(empty($confirmation)) {

		$output = new xarHTML();
		$output->SetInputMode(_XH_VERBATIMINPUT);
		$func = xarVarCleanFromInput('func');
		if($func == "deleteuser" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());
		$output->ConfirmAction(_DELETEUSERSURE,
							   xarModURL('xproject', 'groups',
										'deleteuser'),
							   _CANCEL,
							   xarModURL('xproject', 'groups',
										'view'),
							   array('gid' => $gid,
									'uid' => $uid));
	
		return $output->GetOutput();
    }
    if (xarModAPIFunc('xproject', 'groups',
		     'deleteuser', array('gid' => $gid,
					 'uid' => $uid))) {

		xarSessionSetVar('statusmsg', _USERDELETED);
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}

/*
 * modifygroup - modify group details
 */
function xproject_groups_modifygroup()
{
    list($gname,
	 $gid) = xarVarCleanFromInput('gname',
				     'gid');
    $output = new xarHTML();

    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
	if($func == "modifygroup" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());

    if (!xarSecAuthAction(0, 'Groups::', '$gname::$gid', ACCESS_EDIT)) {
        $output->Text(_GROUPSADDNOAUTH);
        return $output->GetOutput();
    }

    $output->TableStart(_MODIFYGROUP);
    $output->LineBreak();
    $output->FormStart(xarModURL('xproject', 'groups', 'renamegroup'));
    $output->Text(_GROUXARAME);
    $output->FormText('gname', $gname, 20, 20);
    $output->FormHidden('gid', $gid);
    $output->FormHidden('authid', xarSecGenAuthKey());
    $output->FormSubmit(_RENAMEGROUP);
    $output->TableEnd();

    return $output->GetOutput();
}

/*
 * renameGroup - rename group
 * @param $gid - passed to adminapi
 * @param $gname - passed to adminapi
 */
function xproject_groups_renamegroup()
{
    list($gid,
	 $gname,
	 $confirmation) = xarVarCleanFromInput('gid',
					      'gname',
					      'confirmation');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
    if (!xarModAPILoad('xproject', 'groups')) {
	xarSessionSetVar('errormsg', _APILOADFAILED);
	xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
    if (empty($confirmation)) {

	$output = new xarHTML();
	$func = xarVarCleanFromInput('func');
	if($func == "renamegroup" && xarModLoad('xproject','user')) $output->Text(xproject_usermenu());
	$output->ConfirmAction(_RENAMEGROUPSURE,
						   xarModURL('xproject', 'groups',
									'renamegroup'),
						   _CANCEL,
						   xarModURL('xproject', 'groups',
									'view'),
						   array('gid' => $gid,
				 'gname' => $gname));

	return $output->GetOutput();
    }
    if (xarModAPIFunc('xproject', 'groups',
		     'renamegroup', array('gid'   => $gid,
					  'gname' => $gname))) {

	xarSessionSetVar('statusmsg', _GROUPRENAMED);
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}
?>