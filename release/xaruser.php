<?php
// File: $Id$
// ----------------------------------------------------------------------
// Xaraya eXtensible Management System
// Copyright (C) 2002 by the Xaraya Development Team.
// http://www.xaraya.org
// ----------------------------------------------------------------------
// Original Author of file: John Cox via phpMailer Team
// Purpose of file: standard mail output
// ----------------------------------------------------------------------

function release_user_main()
{
    if (!xarSecAuthAction(0, 'Reccomend::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }
    
    $theme = xarVarCleanFromInput('theme');
        
    return array();

}

function release_user_viewids()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $data = array();

    // The user API function is called. 
    $items = xarModAPIFunc('release',
                           'user',
                           'getallids');

    if (empty($items)) {
        $msg = xarML('There are no items to display in the release module');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    $uid = xarUserGetVar('uid');

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        $uid = xarUserGetVar('uid');
        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['name'] = xarVarPrepForDisplay($item['name']);
        $items[$i]['desc'] = nl2br(xarVarPrepHTMLDisplay($item['desc']));
        $items[$i]['type'] = xarVarPrepForDisplay($item['type']);
        $items[$i]['edittitle'] = xarML('Edit');
        $items[$i]['addtitle'] = xarML('Add Release Note');
        $items[$i]['contacturl'] = xarModURL('users',
                                             'user',
                                             'display',
                                              array('uid' => $item['uid']));


        if (($uid == $item['uid']) or (xarSecAuthAction(0, 'release::', "::", ACCESS_EDIT))) {
            $items[$i]['editurl'] = xarModURL('release',
                                              'user',
                                              'modifyid',
                                               array('rid' => $item['rid']));
        } else {
            $items[$i]['editurl'] = '';
        }

        if (($uid == $item['uid']) or (xarSecAuthAction(0, 'release::', "::", ACCESS_EDIT))) {
            $items[$i]['addurl'] = xarModURL('release',
                                              'user',
                                              'addnotes',
                                               array('rid' => $item['rid'],
                                                     'phase' => 'start'));
        } else {
            $items[$i]['addurl'] = '';
        }
    }

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;
}

function release_user_modifyid()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'modify';
    }

    switch(strtolower($phase)) {

        case 'modify':
        default:
            
            $rid = xarVarCleanFromInput('rid');

            // The user API function is called.
            $data = xarModAPIFunc('release',
                                  'user',
                                  'getid',
                                  array('rid' => $rid));

            if ($data == false) return;

            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'update':

            list($rid,
                 $uid,
                 $name,
                 $desc,
                 $idtype) = xarVarCleanFromInput('rid',
                                                 'uid',
                                                 'name',
                                                 'desc',
                                                 'idtype');
            
            // Get the UID of the person submitting the module
            $uid = xarUserGetVar('uid');

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'updateid',
                                array('rid' => $rid,
                                      'uid' => $uid,
                                      'name' => $name,
                                      'desc' => $desc,
                                      'type' => $idtype))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'viewids'));

            return true;

            break;
    }   
    
    return $data;
}

function release_user_addid()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'add';
    }

    switch(strtolower($phase)) {

        case 'add':
        default:

            $data['uid'] = xarUserGetVar('uid');
            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'update':

            list($rid,
                 $uid,
                 $name,
                 $desc,
                 $idtype) = xarVarCleanFromInput('rid',
                                                 'uid',
                                                 'name',
                                                 'desc',
                                                 'idtype');
            
            // Get the UID of the person submitting the module
            $uid = xarUserGetVar('uid');

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'createid',
                                array('rid' => $rid,
                                      'uid' => $uid,
                                      'name' => $name,
                                      'desc' => $desc,
                                      'type' => $idtype))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'viewids'));

            return true;

            break;
    }   
    
    return $data;
}

// Begin Release Data Portion

function release_user_addnotes()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $phase = xarVarCleanFromInput('phase');

    if (empty($phase)){
        $phase = 'getmodule';
    }

    switch(strtolower($phase)) {
        case 'getmodule':
        default:
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'addnote_getmodule', array('authid'    => $authid));

            break;

        case 'start':
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

            $rid = xarVarCleanFromInput('rid');

            // The user API function is called.
            $data = xarModAPIFunc('release',
                                  'user',
                                  'getid',
                                  array('rid' => $rid));

            
            $uid = xarUserGetVar('uid');

            if (($data['uid'] == $uid) or (xarSecAuthAction(0, 'release::', "::", ACCESS_EDIT))) {
                $message = '';
            } else {
                $message = xarML('You are not allowed to add a release notification to this module');               
            }

            //TODO FIX ME!!!
            if (empty($data['name'])){
                $message = xarML('There is no assigned ID for your module or theme.');
            }

            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Release'))
                       .' :: '.xarVarPrepForDisplay($data['name']));

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'addnote_start', array('rid'       => $data['rid'],
                                                                          'name'      => $data['name'],
                                                                          'desc'      => $data['desc'],
                                                                          'message'   => $message,
                                                                          'authid'    => $authid));

            break;

        case 'getbasics':

           list($rid,
                $name) = xarVarCleanFromInput('rid',
                                              'name');

           if (!xarSecConfirmAuthKey()) return;

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_getbasics', array('rid'       => $rid,
                                                                             'name'     => $name,
                                                                             'authid'   => $authid));
            break;
        
        case 'update':

            list($rid,
                 $uid,
                 $name,
                 $desc,
                 $idtype) = xarVarCleanFromInput('rid',
                                                 'uid',
                                                 'name',
                                                 'desc',
                                                 'idtype');
            
            // Get the UID of the person submitting the module
            $uid = xarUserGetVar('uid');

            // Confirm authorisation code
            if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'updateid',
                                array('rid' => $rid,
                                      'uid' => $uid,
                                      'name' => $name,
                                      'desc' => $desc,
                                      'type' => $idtype))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'viewids'));

            return true;

            break;
    }   
    
    return $data;
}

?>