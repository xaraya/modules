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
    
    xarResponseRedirect(xarModURL('release',
                                  'user',
                                  'viewnotes'));

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

        // Basic Information
        $items[$i]['rid'] = xarVarPrepForDisplay($item['rid']);
        $items[$i]['name'] = xarVarPrepForDisplay($item['name']);

        $getuser = xarModAPIFunc('users',
                                 'user',
                                 'get',
                                  array('uid' => $item['uid']));

        // Author Name and Contact URL
        $items[$i]['author'] = $getuser['name'];
        $items[$i]['contacturl'] = xarModURL('users',
                                             'user',
                                             'display',
                                              array('uid' => $item['uid']));
        
        // InfoURL
        $items[$i]['infourl'] = xarModURL('release',
                                          'user',
                                          'display',
                                          array('rid' => $item['rid']));
        $items[$i]['infotitle'] = xarML('View');

        // Edit
        if (($uid == $item['uid']) or (xarSecAuthAction(0, 'release::', "::", ACCESS_EDIT))) {
            $items[$i]['editurl'] = xarModURL('release',
                                              'user',
                                              'modifyid',
                                               array('rid' => $item['rid']));
            $items[$i]['edittitle'] = xarML('Edit');
        } else {
            $items[$i]['edittitle'] = '';
            $items[$i]['editurl'] = '';
        }
        
        // Add Release Note URL
        if (($uid == $item['uid']) or (xarSecAuthAction(0, 'release::', "::", ACCESS_EDIT))) {
            $items[$i]['addurl'] = xarModURL('release',
                                              'user',
                                              'addnotes',
                                               array('rid' => $item['rid'],
                                                     'phase' => 'start'));
            $items[$i]['addtitle'] = xarML('Add');
        } else {
            $items[$i]['addurl'] = '';
            $items[$i]['addtitle'] = '';
        }

        // Add Docs URL
        if (($uid == $item['uid']) or (xarSecAuthAction(0, 'release::', "::", ACCESS_EDIT))) {
            $items[$i]['adddocs'] = xarModURL('release',
                                              'user',
                                              'adddocs',
                                               array('rid' => $item['rid'],
                                                     'phase' => 'start'));
            $items[$i]['adddocstitle'] = xarML('Add');
        } else {
            $items[$i]['adddocs'] = '';
            $items[$i]['adddocstitle'] = '';
        }

        // Get Comments
        $items[$i]['comments'] = xarModAPIFunc('comments',
                                               'user',
                                               'get_count',
                                               array('modid' => xarModGetIDFromName('release'),
                                                     'objectid' => $item['rid']));
        
        if (!$items[$i]['comments']) {
            $items[$i]['comments'] = '0';
        } elseif ($items[$i]['comments'] == 1) {
            $items[$i]['comments'] .= ' ';
        } else {
            $items[$i]['comments'] .= ' ';
        }

        // Get Hits
        $items[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                               'user',
                                               'get',
                                               array('modname' => 'release',
                                                     'objectid' => $item['rid']));
        
        if (!$items[$i]['hitcount']) {
            $items[$i]['hitcount'] = '0';
        } elseif ($items[$i]['hitcount'] == 1) {
            $items[$i]['hitcount'] .= ' ';
        } else {
            $items[$i]['hitcount'] .= ' ';
        }
    }

    // Add the array of items to the template variables
    $data['items'] = $items;
    return $data;

}

function release_user_display()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    list($rid,
         $phase) = xarVarCleanFromInput('rid',
                                        'phase');

    // The user API function is called. 
    $id = xarModAPIFunc('release',
                         'user',
                         'getid',
                          array('rid' => $rid));


    $getuser = xarModAPIFunc('users',
                             'user',
                             'get',
                              array('uid' => $id['uid']));

    if (empty($phase)){
        $phase = 'view';
    }

    switch(strtolower($phase)) {

        case 'view':
        default:
            $hooks = xarModCallHooks('item',
                                     'display',
                                     $id['rid'],
                                     xarModURL('release',
                                               'user',
                                               'display',
                                               array('rid' => $rid)));
            if (empty($hooks)) {
                $data['hooks'] = '';
            } elseif (is_array($hooks)) {
                $data['hooks'] = join('',$hooks);
            } else {
                $data['hooks'] = $hooks;
            }
            $data['version'] = 0;
            $data['docs'] = 0;
            $data['general'] = 2;
            break;
        
        case 'version':
            // The user API function is called.
            $data['items'] = array();
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'rid' => $rid));

            if (empty($items)){
                $data['message'] = xarML('There is no version history on this module');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                // The user API function is called.
                $getid = xarModAPIFunc('release',
                                       'user',
                                       'getid',
                                       array('rid' => $items[$i]['rid']));

                $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
                $items[$i]['name'] = xarVarPrepForDisplay($getid['name']);
                $items[$i]['displaylink'] =  xarModURL('release',
                                                  'user',
                                                  'displaynote',
                                                   array('rid' => $item['rid']));

                $getuser = xarModAPIFunc('users',
                                         'user',
                                         'get',
                                          array('uid' => $getid['uid']));

                $items[$i]['contacturl'] = xarModURL('users',
                                                     'user',
                                                     'display',
                                                      array('uid' => $getid['uid']));


                $items[$i]['realname'] = $getuser['name'];
                $items[$i]['desc'] = xarVarPrepForDisplay($getid['desc']);

                if ($item['certified'] == 2){
                    $items[$i]['certifiedstatus'] = xarML('Yes');
                } else {
                    $items[$i]['certifiedstatus'] = xarML('No');
                }
                $items[$i]['changelog'] = nl2br($item['changelog']);
                $items[$i]['notes'] = nl2br($item['notes']);

                $items[$i]['comments'] = xarModAPIFunc('comments',
                                                       'user',
                                                       'get_count',
                                                       array('modid' => xarModGetIDFromName('xarbb'),
                                                             'objectid' => $item['rnid']));
                
                if (!$items[$i]['comments']) {
                    $items[$i]['comments'] = '0';
                } elseif ($items[$i]['comments'] == 1) {
                    $items[$i]['comments'] .= ' ';
                } else {
                    $items[$i]['comments'] .= ' ';
                }

                $items[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                                       'user',
                                                       'get',
                                                       array('modname' => 'xarbb',
                                                             'objectid' => $item['rnid']));
                
                if (!$items[$i]['hitcount']) {
                    $items[$i]['hitcount'] = '0';
                } elseif ($items[$i]['hitcount'] == 1) {
                    $items[$i]['hitcount'] .= ' ';
                } else {
                    $items[$i]['hitcount'] .= ' ';
                }

            }
            
            $data['version'] = 2;
            $data['items'] = $items;

            break;

        
        case 'docsmodule':
            $data['mtype'] = 'mgeneral';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general module documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;
       
            break;
        
        case 'docstheme':

            $data['mtype'] = 'tgeneral';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general theme documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;

        case 'docsblockgroups':

            $data['mtype'] = 'bgroups';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no block groups documentation defined');
            }

            
            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;
        
        case 'docsblocks':

            $data['mtype'] = 'mblocks';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no blocks documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;

        case 'docshooks':

            $data['mtype'] = 'mhooks';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no hooks documentation defined');
            }

            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
                $items[$i]['docurl'] = xarModURL('release',
                                                 'user',
                                                 'getdoc',
                                                 array('rdid' => $item['rdid']));
            }


            $data['items'] = $items;

            $data['version'] = 0;
            $data['docs'] = 2;
            $data['general'] = 0;

            break;

    }

// Version History
// View Docs
// Comment on docs
    $data['desc'] = nl2br($id['desc']);
    $data['name'] = $id['name'];
    $data['type'] = $id['type'];
    $data['contacturl'] = xarModUrl('users', 'user', 'email', array('uid' => $id['uid']));
    $data['realname'] = $getuser['name'];
    $data['rid'] = $rid;
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
                 $certified,
                 $idtype) = xarVarCleanFromInput('rid',
                                                 'uid',
                                                 'name',
                                                 'desc',
                                                 'certified',
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
                                      'certified' => $certified,
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
                                      'certified' => '1',
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

           //if (!xarSecConfirmAuthKey()) return;

            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Release'))
                       .' :: '.xarVarPrepForDisplay($name));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_getbasics', array('rid'       => $rid,
                                                                             'name'     => $name,
                                                                             'authid'   => $authid));
            break;
        
        case 'getdetails':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck) = xarVarCleanFromInput('rid',
                                                    'name',
                                                    'version',
                                                    'pricecheck',
                                                    'supportcheck',
                                                    'democheck');
            
           //if (!xarSecConfirmAuthKey()) return;

            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Release'))
                       .' :: '.xarVarPrepForDisplay($name));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_getdetails', array('rid'         => $rid,
                                                                              'name'        => $name,
                                                                              'authid'      => $authid,
                                                                              'version'     => $version,
                                                                              'pricecheck'  => $pricecheck,
                                                                              'supportcheck' => $supportcheck,
                                                                              'democheck'    => $democheck));

            break;
        
        case 'preview':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck,
                 $dllink,
                 $price,
                 $demolink,
                 $supportlink,
                 $changelog,
                 $notes) = xarVarCleanFromInput('rid',
                                                'name',
                                                'version',
                                                'pricecheck',
                                                'supportcheck',
                                                'democheck',
                                                'dllink',
                                                'price',
                                                'demolink',
                                                'supportlink',
                                                'changelog',
                                                'notes');
            
           //if (!xarSecConfirmAuthKey()) return;

           $notesf = nl2br($notes);
           $changelogf = nl2br($changelog);

            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Release'))
                       .' :: '.xarVarPrepForDisplay($name));

           $authid = xarSecGenAuthKey();
           $data = xarTplModule('release','user', 'addnote_preview',    array('rid'         => $rid,
                                                                              'name'        => $name,
                                                                              'authid'      => $authid,
                                                                              'version'     => $version,
                                                                              'pricecheck'  => $pricecheck,
                                                                              'supportcheck'=> $supportcheck,
                                                                              'democheck'   => $democheck,
                                                                              'dllink'      => $dllink,
                                                                              'price'       => $price,
                                                                              'demolink'    => $demolink,
                                                                              'supportlink' => $supportlink,
                                                                              'changelog'   => $changelog,
                                                                              'changelogf'  => $changelogf,
                                                                              'notesf'      => $notesf,
                                                                              'notes'       => $notes));



            break;

        case 'update':

            list($rid,
                 $name,
                 $version,
                 $pricecheck,
                 $supportcheck,
                 $democheck,
                 $dllink,
                 $price,
                 $demolink,
                 $supportlink,
                 $changelog,
                 $notes) = xarVarCleanFromInput('rid',
                                                'name',
                                                'version',
                                                'pricecheck',
                                                'supportcheck',
                                                'democheck',
                                                'dllink',
                                                'price',
                                                'demolink',
                                                'supportlink',
                                                'changelog',
                                                'notes');
            
           //if (!xarSecConfirmAuthKey()) return;

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'createnote',
                                array('rid'         => $rid,
                                      'version'     => $version,
                                      'price'       => $pricecheck,
                                      'supported'   => $supportcheck,
                                      'demo'        => $democheck,
                                      'dllink'      => $dllink,
                                      'priceterms'  => $price,
                                      'demolink'    => $demolink,
                                      'supportlink' => $supportlink,
                                      'changelog'   => $changelog,
                                      'notes'       => $notes))) return;

            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Release'))
                       .' :: '.xarVarPrepForDisplay(xarML('Thank You')));

           $data = xarTplModule('release','user', 'addnote_thanks');

            break;
    }   
    
    return $data;
}

function release_user_viewnotes()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get parameters
    list($startnum,
         $phase,
         $filter,
         $type) = xarVarCleanFromInput('startnum',
                                       'phase',
                                       'filter',
                                       'type');
    
    $uid = xarUserGetVar('uid');
    $data['items'] = array();

    if (empty($phase)){
        $phase = 'viewall';
    }

    switch(strtolower($phase)) {

        case 'viewall':
        default:

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'approved' => 2));
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'certified':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'certified'=> 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'price':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'price'    => 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'free':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'price'    => 1));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;

        case 'supported':

            // The user API function is called.
            $items = xarModAPIFunc('release',
                                   'user',
                                   'getallnotes',
                                  array('startnum' => $startnum,
                                        'numitems' => xarModGetVar('users',
                                                                  'itemsperpage'),
                                        'supported'=> 2));
            
            if ($items == false){
                $data['message'] = xarML('There are no releases based on your filters');
            }

            break;
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarModAPIFunc('release',
                               'user',
                               'getid',
                               array('rid' => $items[$i]['rid']));

        $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
        $items[$i]['name'] = xarVarPrepForDisplay($getid['name']);

        $items[$i]['displaylink'] =  xarModURL('release',
                                               'user',
                                               'displaynote',
                                                array('rnid' => $item['rnid']));

        $getuser = xarModAPIFunc('users',
                                 'user',
                                 'get',
                                  array('uid' => $getid['uid']));

        $items[$i]['contacturl'] = xarModURL('users',
                                             'user',
                                             'display',
                                              array('uid' => $getid['uid']));


        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = xarVarPrepForDisplay($getid['desc']);

        if ($item['certified'] == 2){
            $items[$i]['certifiedstatus'] = xarML('Yes');
        } else {
            $items[$i]['certifiedstatus'] = xarML('No');
        }
        $items[$i]['changelog'] = nl2br($item['changelog']);
        $items[$i]['notes'] = nl2br($item['notes']);

        $items[$i]['comments'] = xarModAPIFunc('comments',
                                               'user',
                                               'get_count',
                                               array('modid' => xarModGetIDFromName('release'),
                                                     'objectid' => $item['rnid']));
        
        if (!$items[$i]['comments']) {
            $items[$i]['comments'] = '0';
        } elseif ($items[$i]['comments'] == 1) {
            $items[$i]['comments'] .= ' ';
        } else {
            $items[$i]['comments'] .= ' ';
        }

        $items[$i]['hitcount'] = xarModAPIFunc('hitcount',
                                               'user',
                                               'get',
                                               array('modname' => 'release',
                                                     'objectid' => $item['rnid']));
        
        if (!$items[$i]['hitcount']) {
            $items[$i]['hitcount'] = '0';
        } elseif ($items[$i]['hitcount'] == 1) {
            $items[$i]['hitcount'] .= ' ';
        } else {
            $items[$i]['hitcount'] .= ' ';
        }

    }


    // Add the array of items to the template variables
    $data['items'] = $items;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;

}

function release_user_rssviewnotes()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // Get parameters
    $startnum = xarVarCleanFromInput('startnum');
    
    $uid = xarUserGetVar('uid');
    $data['items'] = array();

    // The user API function is called.
    $items = xarModAPIFunc('release',
                           'user',
                           'getallnotes',
                          array('startnum' => $startnum,
                                'numitems' => xarModGetVar('users',
                                                          'itemsperpage'),
                                'certified'=> $filter));
    
    if ($items == false){
        $data['message'] = xarML('There are no releases based on your filters');
    }

    // Check individual permissions for Edit / Delete
    for ($i = 0; $i < count($items); $i++) {
        $item = $items[$i];

        // The user API function is called.
        $getid = xarModAPIFunc('release',
                               'user',
                               'getid',
                               array('rid' => $items[$i]['rid']));

        $items[$i]['type'] = xarVarPrepForDisplay($getid['type']);
        $items[$i]['name'] = xarVarPrepForDisplay($getid['name']);

        $items[$i]['displaylink'] =  xarModURL('release',
                                               'user',
                                               'displaynote',
                                                array('rnid' => $item['rnid']),
                                                '1');

        $getuser = xarModAPIFunc('users',
                                 'user',
                                 'get',
                                  array('uid' => $getid['uid']));

        $items[$i]['contacturl'] = xarModURL('users',
                                             'user',
                                             'display',
                                              array('uid' => $getid['uid']));


        $items[$i]['realname'] = $getuser['name'];
        $items[$i]['desc'] = xarVarPrepForDisplay($getid['desc']);
        $items[$i]['changelog'] = nl2br($item['changelog']);
        $items[$i]['notes'] = nl2br($item['notes']);

    }


    // Add the array of items to the template variables
    $data['items'] = $items;

    // TODO : add a pager (once it exists in BL)
    $data['pager'] = '';

    // Return the template variables defined in this function
    return $data;

}

function release_user_displaynote()
{
    // Security check
    if (!xarSecAuthAction(0, 'release::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    $rnid = xarVarCleanFromInput('rnid');

    // The user API function is called.
    $item = xarModAPIFunc('release',
                          'user',
                          'getnote',
                          array('rnid' => $rnid));

    if ($item == false) return;

    // The user API function is called. 
    $id = xarModAPIFunc('release',
                         'user',
                         'getid',
                          array('rid' => $item['rid']));


    $getuser = xarModAPIFunc('users',
                             'user',
                             'get',
                              array('uid' => $id['uid']));


    $hooks = xarModCallHooks('item',
                             'display',
                             $item['rnid'],
                             xarModURL('release',
                                       'user',
                                       'displaynote',
                                       array('rnid' => $rnid)));
    if (empty($hooks)) {
        $item['hooks'] = '';
    } elseif (is_array($hooks)) {
        $item['hooks'] = join('',$hooks);
    } else {
        $item['hooks'] = $hooks;
    }
    if ($item['certified'] == 2){
        $item['certifiedstatus'] = xarML('Yes');
    } else {
        $item['certifiedstatus'] = xarML('No');
    }

    $item['desc'] = nl2br($id['desc']);
    $item['name'] = $id['name'];
    $item['type'] = $id['type'];
    $item['contacturl'] = xarModUrl('users', 'user', 'email', array('uid' => $id['uid']));
    $item['realname'] = $getuser['name'];

    return $item;
}

// Begin Docs Portion

function release_user_adddocs()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_READ)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    list ($phase,
          $rid,
          $type)= xarVarCleanFromInput('phase',
                                       'rid',
                                       'type');

    $data['items'] = array();
    $data['rid'] = $rid;

    if (empty($phase)){
        $phase = 'getmodule';
    }

    switch(strtolower($phase)) {
        case 'getmodule':
        default:
            // First we need to get the module that we are adding the release note to.
            // This will be done in several stages so that the information is accurate.

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'adddocs_getmodule', array('authid'    => $authid));

            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Documentation')));

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
                $message = xarML('You are not allowed to add documentation to this module');               
            }

            //TODO FIX ME!!!
            if (empty($data['name'])){
                $message = xarML('There is no assigned ID for your module or theme.');
            }

            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Documentation'))
                       .' :: '.xarVarPrepForDisplay($data['name']));

            $authid = xarSecGenAuthKey();
            $data = xarTplModule('release','user', 'adddocs_start', array('rid'       => $data['rid'],
                                                                          'name'      => $data['name'],
                                                                          'desc'      => $data['desc'],
                                                                          'type'      => $data['type'],
                                                                          'message'   => $message,
                                                                          'authid'    => $authid));

            break;

        case 'module':

            $data['mtype'] = 'mgeneral';
            $data['return'] = 'module';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general module documentation defined');
            }

            
            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Documentation'))
                       .' :: '. xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();
        
            break;
        
        case 'theme':

            $data['mtype'] = 'tgeneral';
            $data['return'] = 'theme';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no general theme documentation defined');
            }

            
            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Documentation'))
                       .' :: '. xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();

            break;

        case 'blockgroups':

            $data['mtype'] = 'bgroups';
            $data['return'] = 'blockgroups';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no block groups documentation defined');
            }

            
            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Documentation'))
                       .' :: '. xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();

            break;
        
        case 'blocks':

            $data['mtype'] = 'mblocks';
            $data['return'] = 'blocks';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no blocks documentation defined');
            }

            
            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Documentation'))
                       .' :: '. xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();


            break;

        case 'hooks':

            $data['mtype'] = 'mhooks';
            $data['return'] = 'hooks';
            // The user API function is called. 

            $items = xarModAPIFunc('release',
                                   'user',
                                   'getdocs',
                                    array('rid' => $rid,
                                          'type'=> $data['mtype']));

            if (empty($items)){
                $data['message'] = xarML('There is no hook documentation defined');
            }

            
            xarTplSetPageTitle(xarConfigGetVar('Site.Core.SiteName').' :: '.
                               xarVarPrepForDisplay(xarML('Documentation'))
                       .' :: '. xarVarPrepForDisplay(xarML('General Information')));


            // Check individual permissions for Edit / Delete
            for ($i = 0; $i < count($items); $i++) {
                $item = $items[$i];

                $uid = xarUserGetVar('uid');
                $items[$i]['docsf'] = nl2br(xarVarPrepHTMLDisplay($item['docs']));
            }


            $data['items'] = $items;
            $data['authid'] = xarSecGenAuthKey();

            break;

        case 'update':
            list($rid,
                 $mtype,
                 $title,
                 $return,
                 $doc) = xarVarCleanFromInput('rid',
                                              'mtype',
                                              'title',
                                              'return',
                                              'doc');
            
           if (!xarSecConfirmAuthKey()) return;

           if (!xarSecAuthAction(0, 'release::', '::', ACCESS_EDIT)) {
               $approved = 1;
           } else {
               $approved = 2;
           }

            // The user API function is called. 
            if (!xarModAPIFunc('release',
                               'user',
                               'createdoc',
                                array('rid'         => $rid,
                                      'type'        => $mtype,
                                      'title'       => $title,
                                      'doc'         => $doc,
                                      'approved'    => $approved))) return;

            xarResponseRedirect(xarModURL('release', 'user', 'adddocs', array('phase' => $return, 
                                                                              'rid' => $rid)));

           $data = '';
            break;
    }   
    
    return $data;
}

function release_user_rssviewdocs()
{
    // Security check
    if (!xarSecAuthAction(0, 'users::', '::', ACCESS_OVERVIEW)) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    // The user API function is called. 
    $id = xarModAPIFunc('release',
                         'user',
                         'getallids',
                          array('certified' => '2'));


}

?>
